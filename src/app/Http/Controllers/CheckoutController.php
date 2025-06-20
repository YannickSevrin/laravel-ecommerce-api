<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $cart = $user->cart()->with('product')->get();
        $addresses = $user->addresses()->get();
        $newAddress = old('new_address') ? true : false;

        return view('checkout.index', compact('cart', 'addresses', 'newAddress'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'address_id' => 'required|exists:addresses,id',
        ]);

        $items = $user->cart()->with('product')->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $total = $items->sum(fn($item) => $item->product->price * $item->quantity);

        if ($request->filled('new_address')) {
            $address = $user->addresses()->create([
                'address' => $request->new_address,
                'postal_code' => $request->new_postal_code,
                'city' => $request->new_city,
                'country' => $request->new_country,
                'type' => 'shipping',
            ]);
            $addressId = $address->id;
        } else {
            $request->validate(['address_id' => 'required|exists:addresses,id']);
            $addressId = $request->address_id;
        }

        $order = Order::create([
            'user_id' => $user->id,
            'total' => $total,
            'status' => 'pending',
            'address_id' => $addressId
        ]);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }

        $user->cart()->delete();
        \Mail::to($user->email)->send(new \App\Mail\OrderConfirmation($order));
        return redirect()->route('checkout.thank-you')->with('success', 'Order placed successfully!');
    }
}