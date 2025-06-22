<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'address_id' => 'required_without:new_address|exists:addresses,id',
            'new_address.address' => 'required_with:new_address|string',
            'new_address.postal_code' => 'required_with:new_address|string',
            'new_address.city' => 'required_with:new_address|string',
            'new_address.country' => 'required_with:new_address|string',
        ]);

        $items = $user->cart()->with('product')->get();

        if ($items->isEmpty()) {
            return response()->json([
                'message' => 'Your cart is empty'
            ], 400);
        }

        $total = $items->sum(fn($item) => $item->product->price * $item->quantity);

        // Handle address
        if ($request->has('new_address')) {
            $address = $user->addresses()->create([
                'address' => $request->new_address['address'],
                'postal_code' => $request->new_address['postal_code'],
                'city' => $request->new_address['city'],
                'country' => $request->new_address['country'],
                'type' => 'shipping',
            ]);
            $addressId = $address->id;
        } else {
            $addressId = $request->address_id;
            
            // Verify that the address belongs to the user
            $address = $user->addresses()->find($addressId);
            if (!$address) {
                return response()->json([
                    'message' => 'Address not found'
                ], 404);
            }
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

        // Clear cart
        $user->cart()->delete();

        // Send confirmation email
        try {
            \Mail::to($user->email)->send(new \App\Mail\OrderConfirmation($order));
        } catch (\Exception $e) {
            // Log error but don't fail the order
            \Log::error('Error sending order confirmation email: ' . $e->getMessage());
        }

        $order->load('items.product', 'address');

        return response()->json([
            'message' => 'Order created successfully',
            'data' => $order
        ], 201);
    }
} 