<?php

namespace App\Services;

use App\Models\CenterProductStock;
use App\Models\Order;
use App\Models\StockMovement;
use DomainException;
use Illuminate\Support\Facades\DB;
use LogicException;

class StockService
{
    /* ------------ APPROVED → stock commit ------------ */
    public static function commit(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {

                /* -------- transfer (two movements) -------- */
                if ($order->flow === 'transfer') {
                    if ($order->to_center_id === null) {
                        throw new LogicException('Transfer order missing to_center_id');
                    }

                    // source → transfer_out
                    self::applyMovement(
                        centerId: $order->center_id,
                        item: $item,
                        delta: -$item->quantity,
                        type: 'transfer_out'
                    );

                    // destination → transfer_in
                    self::applyMovement(
                        centerId: $order->to_center_id,
                        item: $item,
                        delta: +$item->quantity,
                        type: 'transfer_in'
                    );

                    continue;      // ✅ skip the rest of the loop
                }

                /* -------- in / out / adjust -------- */
                self::applyMovement(
                    centerId: $order->center_id,
                    item: $item,
                    delta: self::deltaForSource($order->flow, $item->quantity),
                    type: $order->flow
                );
            }
        });
    }


    private static function applyMovement(int $centerId, $item, int $delta, string $type): void
    {
        $stock = CenterProductStock::lockForUpdate()->firstOrCreate([
            'center_id' => $centerId,
            'product_id' => $item->product_id,
        ]);

        if ($delta < 0 && ($stock->quantity + $delta) < 0) {
            throw new DomainException("لا توجد كمية كافية من المنتج: {$item->product->name}");
        }

        $stock->quantity += $delta;
        $stock->save();

        StockMovement::create([
            'center_id' => $centerId,
            'product_id' => $item->product_id,
            'order_id' => $item->order_id,
            'type' => $type,
            'quantity' => $delta,
            'effective_at' => now(),
        ]);
    }

    /* ------------ helpers ---------------------------- */

    private static function deltaForSource(string $flow, int $qty): int
    {
        return match ($flow) {
            'in' => +$qty,
            'out' => -$qty,
            'adjust' => $qty,
            default => 0,
        };
    }

    public static function rollback(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                // reverse source center
                self::applyMovement(
                    centerId: $order->center_id,
                    item: $item,
                    delta: -self::deltaForSource($order->flow, $item->quantity),
                    type: 'rollback'
                );

                // reverse destination for transfer
                if ($order->flow === 'transfer' && $order->to_center_id) {
                    self::applyMovement(
                        centerId: $order->to_center_id,
                        item: $item,
                        delta: -$item->quantity,
                        type: 'rollback'
                    );
                }
            }
        });
    }
}
