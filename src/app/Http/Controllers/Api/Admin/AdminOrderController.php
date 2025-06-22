<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()->with(['user:id,name,email', 'items']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by order ID or user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%')
                               ->orWhere('email', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $perPage = $request->get('per_page', 15);
        $orders = $query->latest()->paginate($perPage);

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
        $order->load(['items.product', 'user', 'address']);

        return response()->json([
            'data' => $order
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,shipped,canceled'
        ]);

        $order->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Order status updated successfully',
            'data' => $order
        ]);
    }
} 