<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addresses = auth()->user()->addresses;
        return view('profile.addresses.index', compact('addresses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required',
            'postal_code' => 'required',
            'city' => 'required',
            'country' => 'required',
        ]);

        auth()->user()->addresses()->create([
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'city' => $request->city,
            'country' => $request->country,
            'type' => 'shipping',
        ]);

        return back()->with('success', 'Address added successfully.');
    }

    public function setDefault(Address $address)
{
    $user = auth()->user();

    if ($address->user_id !== $user->id) {
        abort(403);
    }

    $user->addresses()->update(['is_default' => false]);
    $address->update(['is_default' => true]);

    return back()->with('success', 'Default address set.');
}
}
