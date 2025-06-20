<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class CartController extends Controller
{
    public function index()
    {
        $items = auth()->user()
            ->cart()
            ->with('product')
            ->get();

        return view('cart.index', compact('items'));
    }

    public function add(Request $request, Product $product)
    {
        $user = auth()->user();

        $item = $user->cart()->firstOrNew(['product_id' => $product->id]);
        $item->quantity = ($item->quantity ?? 0) + 1;
        $item->save();

        return redirect()->route('cart.index')->with('success', 'Product added to cart');
    }

    public function remove(Product $product)
    {
        auth()->user()
            ->cart()
            ->where('product_id', $product->id)
            ->delete();

        return redirect()->route('cart.index')->with('success', 'Product removed from cart');
    }

    public function checkout()
    {
        $user = auth()->user();
        $items = $user->cart()->with('product')->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $total = $items->sum(fn($item) => $item->product->price * $item->quantity);

        $order = Order::create([
            'user_id' => $user->id,
            'total' => $total,
            'status' => 'pending',
        ]);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }

        // Clear cart
        $user->cart()->delete();

        return redirect()->route('cart.index')->with('success', 'Order created successfully ✅');
    }
}
