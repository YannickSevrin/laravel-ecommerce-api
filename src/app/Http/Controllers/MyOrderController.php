<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;

class MyOrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()
            ->orders()
            ->latest()
            ->withCount('items')
            ->paginate(10);

        return view('my-orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        
        $order->load('items.product', 'user.shippingAddress');
        return view('my-orders.show', compact('order'));
    }
}