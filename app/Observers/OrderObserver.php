<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\StockService;
use DomainException;

class OrderObserver
{
    public function updating(Order $order): void
    {
        if (!$order->isDirty('status')) return;

        [$from, $to] = [$order->getOriginal('status'), $order->status];

        match ([$from, $to]) {
            ['pending', 'approved'] => StockService::commit($order),
            ['approved', 'canceled'] => StockService::rollback($order),
            ['pending', 'rejected'],
            ['pending', 'canceled'] => null,
            default => throw new DomainException("Invalid status transition from $from to $to"),
        };
    }

    public function deleting(Order $order): void
    {
        if ($order->status === 'approved') {
            throw new DomainException(
                'لا يمكن حذف طلب مؤكد، يرجى إلغاء الطلب قبل الحذف'
            );
        }
    }

    public function forceDeleted(Order $order): void
    {
        throw new DomainException('Force-delete disabled; use cancel/reject instead.');
    }
}
