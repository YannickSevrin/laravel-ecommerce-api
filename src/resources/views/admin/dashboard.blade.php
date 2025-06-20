<x-admin-layout title="Dashboard">
    <h1 class="text-2xl font-bold mb-6">Dashboard 🧠</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-lg font-semibold">🛒 Products</h2>
            <p class="text-2xl">{{ $productsCount }}</p>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-lg font-semibold">📁 Categories</h2>
            <p class="text-2xl">{{ $categoriesCount }}</p>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-lg font-semibold">👥 Users</h2>
            <p class="text-2xl">{{ $usersCount }}</p>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-lg font-semibold">📦 Orders</h2>
            <p class="text-2xl">{{ $ordersCount }}</p>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-lg font-semibold">💰 Total Sales</h2>
            <p class="text-2xl">{{ number_format($salesTotal, 2) }} €</p>
        </div>
    </div>
</x-admin-layout>
