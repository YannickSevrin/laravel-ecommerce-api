<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">🛍️ Our Products</h2>
    </x-slot>
    
    <div class="py-8 max-w-7xl mx-auto px-4 space-y-6">
    <form method="GET" action="{{ route('shop.index') }}" class="mb-6 space-y-2">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            {{-- Search --}}
            <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Search products..."
                class="border p-2 rounded w-full">

            {{-- Category --}}
            <select name="category" class="border p-2 rounded w-full">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            {{-- Min price --}}
            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min Price"
                class="border p-2 rounded w-full">

            {{-- Max price --}}
            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max Price"
                class="border p-2 rounded w-full">
        </div>

        <div class="flex justify-between items-center mt-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">🔍 Filter</button>

            @if(request()->filled(['category', 'search', 'min_price', 'max_price']))
                <a href="{{ route('shop.index') }}" class="text-sm text-red-500 underline">Clear filters</a>
            @endif
        </div>
    </form>
    @if(request('category'))
    <h3 class="text-lg mb-2 text-gray-700">
        Showing products in <strong>
        {{ $categories->firstWhere('id', request('category'))?->name }}</strong>
    </h3>
    @endif
    @if($products->count() === 0)
    <p class="text-gray-500">No products found matching your filters.</p>
    @endif
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($products as $product)
                <div class="border rounded shadow p-4 flex flex-col justify-between">
                    <div>
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover mb-4">
                        @endif

                        <h3 class="font-bold text-lg">{{ $product->name }}</h3>
                        <p class="text-gray-600 text-sm">{{ $product->category->name }}</p>
                        <p class="mt-2 font-semibold">{{ number_format($product->price, 2) }} €</p>
                    </div>

                    <div class="mt-4 space-y-2">
                        <a href="{{ route('shop.show', $product) }}" class="text-blue-600 underline">Details</a>

                        <form method="POST" action="{{ route('cart.add', $product) }}">
                            @csrf
                            <button class="bg-blue-600 text-white px-3 py-1 rounded w-full">➕ Add to Cart</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div>
            {{ $products->links() }}
        </div>
    </div>
</x-app-layout>