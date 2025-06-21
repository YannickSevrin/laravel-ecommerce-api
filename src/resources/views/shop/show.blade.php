<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">{{ $product->name }}</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4">
        <div class="bg-white shadow rounded p-6 space-y-4">
            @if ($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover mb-4">
            @endif

            <p class="text-gray-600 text-sm">Category: {{ $product->category->name }}</p>
            <p class="text-lg font-bold">{{ number_format($product->price, 2) }} €</p>
            <p class="text-gray-700">{{ $product->description }}</p>

            <form method="POST" action="{{ route('cart.add', $product) }}">
                @csrf
                <button class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">➕ Add to Cart</button>
            </form>
        </div>
    </div>
</x-app-layout>