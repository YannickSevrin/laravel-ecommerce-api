<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $items = auth()->user()
            ->cart()
            ->with('product')
            ->get();

        $total = $items->sum(fn($item) => $item->product->price * $item->quantity);

        return response()->json([
            'data' => $items,
            'meta' => [
                'total' => $total,
                'items_count' => $items->count(),
                'total_quantity' => $items->sum('quantity')
            ]
        ]);
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'nullable|integer|min:1|max:100'
        ]);

        $user = auth()->user();
        $quantity = $request->get('quantity', 1);

        $item = $user->cart()->firstOrNew(['product_id' => $product->id]);
        $item->quantity = ($item->quantity ?? 0) + $quantity;
        $item->save();

        return response()->json([
            'message' => 'Product added to cart',
            'data' => $item->load('product')
        ], 201);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:100'
        ]);

        $user = auth()->user();
        $item = $user->cart()->where('product_id', $product->id)->first();

        if (!$item) {
            return response()->json([
                'message' => 'Product not found in cart'
            ], 404);
        }

        $item->quantity = $request->quantity;
        $item->save();

        return response()->json([
            'message' => 'Quantity updated',
            'data' => $item->load('product')
        ]);
    }

    public function remove(Product $product)
    {
        $user = auth()->user();
        $deleted = $user->cart()->where('product_id', $product->id)->delete();

        if (!$deleted) {
            return response()->json([
                'message' => 'Product not found in cart'
            ], 404);
        }

        return response()->json([
            'message' => 'Product removed from cart'
        ]);
    }

    public function clear()
    {
        auth()->user()->cart()->delete();

        return response()->json([
            'message' => 'Cart cleared'
        ]);
    }
} 