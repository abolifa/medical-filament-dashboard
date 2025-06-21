<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderPrintController extends Controller
{
    public function show(Order $order)
    {
        $order->load('items.product', 'center', 'supplier', 'patient', 'toCenter');
        return view('printables.order', compact('order'));
    }
}
