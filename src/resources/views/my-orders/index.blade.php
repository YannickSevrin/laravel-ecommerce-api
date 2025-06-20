<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            🧾 My Orders
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($orders->isEmpty())
                <p class="text-gray-600">You have not placed any orders yet.</p>
            @else
                <table class="w-full text-left border-collapse mb-6">
                    <thead>
                        <tr>
                            <th class="border px-4 py-2">Order #</th>
                            <th class="border px-4 py-2">Date</th>
                            <th class="border px-4 py-2">Total</th>
                            <th class="border px-4 py-2">Items</th>
                            <th class="border px-4 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td class="border px-4 py-2">#{{ $order->id }}</td>
                                <td class="border px-4 py-2">{{ $order->created_at->format('d M Y') }}</td>
                                <td class="border px-4 py-2">{{ number_format($order->total, 2) }} €</td>
                                <td class="border px-4 py-2">{{ $order->items_count }}</td>
                                <td class="border px-4 py-2">
                                    @php
                                        $statusIcons = [
                                            'pending' => '⏳',
                                            'paid' => '✅',
                                            'shipped' => '🚚',
                                            'canceled' => '❌',
                                        ];
                                    @endphp
                                    {{ $statusIcons[$order->status] ?? '' }} {{ ucfirst($order->status) }}
                                </td>
                                <td class="border px-4 py-2">
                                    <a href="{{ route('my-orders.show', $order) }}" class="text-blue-600 hover:underline">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $orders->links() }}
            @endif
        </div>
    </div>
</x-app-layout>