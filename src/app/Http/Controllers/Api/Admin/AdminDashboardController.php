<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Order;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'products' => [
                'count' => Product::count(),
                'recent' => Product::latest()->take(5)->get()
            ],
            'categories' => [
                'count' => Category::count(),
                'list' => Category::withCount('products')->get()
            ],
            'users' => [
                'count' => User::count(),
                'customers' => User::where('role', 'customer')->count(),
                'admins' => User::where('role', 'admin')->count(),
                'recent' => User::latest()->take(5)->select('id', 'name', 'email', 'created_at')->get()
            ],
            'orders' => [
                'count' => Order::count(),
                'pending' => Order::where('status', 'pending')->count(),
                'paid' => Order::where('status', 'paid')->count(),
                'shipped' => Order::where('status', 'shipped')->count(),
                'canceled' => Order::where('status', 'canceled')->count(),
                'recent' => Order::with(['user:id,name,email', 'items'])
                    ->latest()
                    ->take(10)
                    ->get()
            ],
            'sales' => [
                'total' => Order::sum('total'),
                'monthly' => Order::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('total'),
                'daily' => Order::whereDate('created_at', today())->sum('total')
            ]
        ];

        return response()->json([
            'data' => $stats
        ]);
    }
} 