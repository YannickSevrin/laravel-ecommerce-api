<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            🛒 My cart
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if ($items->isEmpty())
                <p class="text-gray-600">Your cart is empty.</p>
            @else
                <table class="w-full text-left border-collapse mb-6">
                    <thead>
                        <tr>
                            <th class="border px-4 py-2">Product</th>
                            <th class="border px-4 py-2">Quantity</th>
                            <th class="border px-4 py-2">Price</th>
                            <th class="border px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td class="border px-4 py-2">{{ $item->product->name }}</td>
                                <td class="border px-4 py-2">{{ $item->quantity }}</td>
                                <td class="border px-4 py-2">{{ number_format($item->product->price * $item->quantity, 2) }} €</td>
                                <td class="border px-4 py-2">
                                    <form action="{{ route('cart.remove', $item->product) }}" method="POST">
                                        @csrf
                                        <button class="text-red-600 hover:underline" onclick="return confirm('Remove this product?')">🗑️ Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @php
                    $total = $items->sum(fn($item) => $item->quantity * $item->product->price);
                @endphp

                <div class="text-right space-y-4">
                    <p class="text-lg font-bold">Total: {{ number_format($total, 2) }} €</p>

                    <a href="{{ route('checkout.index') }}" class="bg-green-600 text-white px-4 py-2 rounded">✅ Checkout</a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>