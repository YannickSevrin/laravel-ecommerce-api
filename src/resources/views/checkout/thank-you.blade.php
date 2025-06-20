<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">✅ Thank You</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow text-center">
            <h1 class="text-2xl font-bold mb-4">🎉 Your order has been placed!</h1>
            <p class="mb-4">You will receive a confirmation email shortly.</p>
            <a href="{{ route('my-orders.index') }}" class="text-blue-600 underline">View my orders</a>
        </div>
    </div>
</x-app-layout>