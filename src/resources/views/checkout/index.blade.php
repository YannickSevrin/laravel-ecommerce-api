<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            🧾 Checkout
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if (session('error'))
                <div class="bg-red-100 text-red-700 p-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if ($cart->isEmpty())
                <p class="text-gray-600">Your cart is empty.</p>
            @else
                <div class="bg-white p-6 shadow rounded">
                    <h3 class="text-lg font-semibold mb-4">🛒 Order Summary</h3>
                    <ul class="space-y-2">
                        @php $total = 0; @endphp
                        @foreach ($cart as $item)
                            @php
                                $lineTotal = $item->quantity * $item->product->price;
                                $total += $lineTotal;
                            @endphp
                            <li class="flex justify-between">
                                <span>{{ $item->product->name }} x {{ $item->quantity }}</span>
                                <span>{{ number_format($lineTotal, 2) }} €</span>
                            </li>
                        @endforeach
                    </ul>
                    <p class="text-right font-bold mt-4">Total: {{ number_format($total, 2) }} €</p>
                </div>

                <div class="bg-white p-6 shadow rounded">
                    <h3 class="text-lg font-semibold mb-4">📦 Select Shipping Address</h3>

                    <form action="{{ route('checkout.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <select name="address_id" required class="w-full border rounded p-2">
                            <option value="">-- Choose an address --</option>
                            @foreach ($addresses as $address)
                                <option value="{{ $address->id }}">
                                    {{ $address->address }} - {{ $address->postal_code }} {{ $address->city }} ({{ $address->country }})
                                </option>
                            @endforeach
                        </select>
                        <div>
                            <label class="block mb-1 font-semibold">➕ Or enter a new address</label>
                            <textarea name="new_address" rows="2" class="w-full border rounded p-2" placeholder="New shipping address...">{{ old('new_address') }}</textarea>
                        </div>

                        <div class="flex space-x-4">
                            <input type="text" name="new_postal_code" class="w-1/3 border p-2 rounded" placeholder="Postal Code" value="{{ old('new_postal_code') }}">
                            <input type="text" name="new_city" class="w-1/3 border p-2 rounded" placeholder="City" value="{{ old('new_city') }}">
                            <input type="text" name="new_country" class="w-1/3 border p-2 rounded" placeholder="Country" value="{{ old('new_country') }}">
                        </div>

                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                            ✅ Confirm Order
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>