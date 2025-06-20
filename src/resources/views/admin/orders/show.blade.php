<x-admin-layout title="Order #{{ $order->id }}">
    <h1 class="text-2xl font-bold mb-4">🧾 Order #{{ $order->id }}</h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6">
        <p><strong>Customer:</strong> {{ $order->user->name }} ({{ $order->user->email }})</p>
        <p><strong>Date:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Total:</strong> {{ number_format($order->total, 2) }} €</p>
    </div>

    <h2 class="text-xl font-semibold mb-2">🛍️ Ordered products</h2>

    <table class="w-full text-left border-collapse mb-6">
        <thead>
            <tr>
                <th class="border px-4 py-2">Product</th>
                <th class="border px-4 py-2">Unit price</th>
                <th class="border px-4 py-2">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td class="border px-4 py-2">{{ $item->product->name ?? '-' }}</td>
                    <td class="border px-4 py-2">{{ number_format($item->price, 2) }} €</td>
                    <td class="border px-4 py-2">{{ $item->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2 class="text-xl font-semibold mb-2">🔄 Update status</h2>

    <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <select name="status" class="border rounded p-2">
            @foreach (['pending', 'paid', 'shipped', 'canceled'] as $status)
                <option value="{{ $status }}" @if($order->status === $status) selected @endif>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>

        <button class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
    </form>
</x-admin-layout>
