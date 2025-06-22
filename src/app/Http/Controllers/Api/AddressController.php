<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = auth()->user()->addresses()->get();

        return response()->json([
            'data' => $addresses
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'postal_code' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'type' => 'nullable|in:billing,shipping',
        ]);

        $address = auth()->user()->addresses()->create([
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'city' => $request->city,
            'country' => $request->country,
            'type' => $request->type ?? 'shipping',
        ]);

        return response()->json([
            'message' => 'Address added successfully',
            'data' => $address
        ], 201);
    }

    public function update(Request $request, Address $address)
    {
        if ($address->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Address not found'
            ], 404);
        }

        $request->validate([
            'address' => 'required|string',
            'postal_code' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'type' => 'nullable|in:billing,shipping',
        ]);

        $address->update($request->validated());

        return response()->json([
            'message' => 'Address updated successfully',
            'data' => $address
        ]);
    }

    public function destroy(Address $address)
    {
        if ($address->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Address not found'
            ], 404);
        }

        $address->delete();

        return response()->json([
            'message' => 'Address deleted successfully'
        ]);
    }

    public function setDefault(Address $address)
    {
        $user = auth()->user();

        if ($address->user_id !== $user->id) {
            return response()->json([
                'message' => 'Address not found'
            ], 404);
        }

        // Remove default status from other addresses
        $user->addresses()->update(['is_default' => false]);
        
        // Set this address as default
        $address->update(['is_default' => true]);

        return response()->json([
            'message' => 'Default address set successfully',
            'data' => $address
        ]);
    }
} 