<?php

use App\Http\Controllers\OrderPrintController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/orders/{order}/print', [OrderPrintController::class, 'show'])
    ->name('orders.print');

require __DIR__ . '/auth.php';
