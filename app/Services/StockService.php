<?php

namespace App\Services;

use App\Models\CenterProductStock;
use App\Models\Order;
use App\Models\StockMovement;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockService
{
    public static function commit(Order $order): void
    {
        Log::info('Stock commit started', [
            'order_id' => $order->id,
            'flow' => $order->flow,
            'item_count' => $order->items->count(),
        ]);

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                Log::info('Processing item', [
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                ]);

                if ($order->flow === 'transfer') {
                    self::applyMovement($order->center_id, $item, -$item->quantity, 'transfer_out');
                    self::applyMovement($order->to_center_id, $item, $item->quantity, 'transfer_in');
                } else {
                    $delta = self::deltaForSource($order->flow, $item->quantity);
                    self::applyMovement($order->center_id, $item, $delta, $order->flow);
                }
            }

            Log::info('Stock commit completed', ['order_id' => $order->id]);
        });
    }

    private static function applyMovement(int $centerId, $item, int $delta, string $type): void
    {
        $stock = CenterProductStock::lockForUpdate()->firstOrCreate([
            'center_id' => $centerId,
            'product_id' => $item->product_id,
        ]);

        if ($delta < 0 && ($stock->quantity + $delta) < 0) {
            Log::warning('Insufficient stock', [
                'center_id' => $centerId,
                'product_id' => $item->product_id,
                'current_quantity' => $stock->quantity,
                'delta' => $delta,
            ]);
            throw new DomainException("Not enough stock for product: {$item->product->name}");
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

    private static function deltaForSource(string $flow, int $qty): int
    {
        return match ($flow) {
            'in' => $qty,
            'out' => -$qty,
            'adjust' => $qty,
            default => 0,
        };
    }

    public static function rollback(Order $order): void
    {
        Log::info('Stock rollback started', [
            'order_id' => $order->id,
            'flow' => $order->flow,
            'item_count' => $order->items->count(),
        ]);

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                // Reverse source center
                $delta = -self::deltaForSource($order->flow, $item->quantity);
                self::applyMovement($order->center_id, $item, $delta, 'rollback');

                // Reverse destination if transfer
                if ($order->flow === 'transfer' && $order->to_center_id) {
                    self::applyMovement($order->to_center_id, $item, -$item->quantity, 'rollback');
                }
            }

            Log::info('Stock rollback completed', ['order_id' => $order->id]);
        });
    }
}
