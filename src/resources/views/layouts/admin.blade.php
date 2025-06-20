<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>Admin - {{ $title ?? 'Dashboard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md p-4">
            <h2 class="text-xl font-bold mb-6">Admin Panel</h2>
            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block text-blue-600 hover:underline">📊 Dashboard</a>
                <a href="{{ route('admin.products.index') }}" class="block text-blue-600 hover:underline">📦 Products</a>
                <a href="{{ route('admin.categories.index') }}" class="block text-blue-600 hover:underline">📁 Categories</a>
                <a href="{{ route('admin.orders.index') }}" class="block text-blue-600 hover:underline">🧾 Orders</a>
                <a href="{{ route('admin.users.index') }}" class="block text-blue-600 hover:underline">👥 Users</a>
                <a href="/" class="block text-gray-500 hover:text-black">🏠 Back to site</a>
            </nav>
        </aside>

        <!-- Main content -->
        <main class="flex-1 p-6">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
