<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Order #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-4 shadow rounded">
                <p><strong>Date:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
                <p><strong>Total:</strong> {{ number_format($order->total, 2) }} €</p>
                @if ($order->user->shippingAddress)
                    <div class="mt-4">
                        <h3 class="font-semibold">📦 Shipping Address</h3>
                        <p>{{ $order->user->shippingAddress->address }}</p>
                        <p>{{ $order->user->shippingAddress->postal_code }} {{ $order->user->shippingAddress->city }}</p>
                        <p>{{ $order->user->shippingAddress->country }}</p>
                    </div>
                @endif
            </div>

            <div class="bg-white p-4 shadow rounded">
                <h3 class="font-bold text-lg mb-2">Items</h3>
                <ul class="space-y-2">
                    @foreach ($order->items as $item)
                        <li class="flex justify-between">
                            <span>{{ $item->product->name }} x {{ $item->quantity }}</span>
                            <span>{{ number_format($item->price * $item->quantity, 2) }} €</span>
                        </li>
                    @endforeach
                </ul>
            </div>

        </div>
    </div>
</x-app-layout>