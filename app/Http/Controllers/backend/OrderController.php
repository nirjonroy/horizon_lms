<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'coupon'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('backend.orders', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('coupon')->findOrFail($id);
        $user = $order->user_id ? User::find($order->user_id) : null;

        return view('backend.show_order', compact('order', 'user'));
    }
}
