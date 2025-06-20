<x-admin-layout title="Orders">
    <h1 class="text-2xl font-bold mb-4">📦 Orders</h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full text-left border-collapse">
        <thead>
            <tr>
                <th class="border px-4 py-2">#</th>
                <th class="border px-4 py-2">Customer</th>
                <th class="border px-4 py-2">Total</th>
                <th class="border px-4 py-2">Status</th>
                <th class="border px-4 py-2">Date</th>
                <th class="border px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td class="border px-4 py-2">#{{ $order->id }}</td>
                    <td class="border px-4 py-2">{{ $order->user->name ?? '-' }}</td>
                    <td class="border px-4 py-2">{{ number_format($order->total, 2) }} €</td>
                    <td class="border px-4 py-2">{{ ucfirst($order->status) }}</td>
                    <td class="border px-4 py-2">{{ $order->created_at->format('d/m/Y') }}</td>
                    <td class="border px-4 py-2">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:underline">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-admin-layout>
