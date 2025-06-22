<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()
            ->orders()
            ->with(['items.product', 'address'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'data' => $orders->items(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
            'links' => [
                'next' => $orders->nextPageUrl(),
                'prev' => $orders->previousPageUrl(),
            ]
        ]);
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        $order->load('items.product', 'address');

        return response()->json([
            'data' => $order
        ]);
    }
} 