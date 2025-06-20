<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            📍 My Addresses
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if (session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Existing Addresses --}}
            <div class="bg-white p-6 shadow rounded">
                <h3 class="text-lg font-bold mb-4">Saved Addresses</h3>

                @forelse ($addresses as $address)
                    <div class="border-b pb-4 mb-4">
                        <p>{{ $address->address }}</p>
                        <p>{{ $address->postal_code }} {{ $address->city }}, {{ $address->country }}</p>

                        <div class="mt-2 flex items-center space-x-4">
                            @if ($address->is_default)
                                <span class="text-green-600 font-semibold">✅ Default</span>
                            @else
                                <form method="POST" action="{{ route('profile.addresses.default', $address) }}">
                                    @csrf
                                    <button class="text-blue-600 hover:underline">Set as default</button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('profile.addresses.destroy', $address) }}">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline" onclick="return confirm('Delete this address?')">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-600">You have no saved addresses.</p>
                @endforelse
            </div>

            {{-- Add new address --}}
            <div class="bg-white p-6 shadow rounded">
                <h3 class="text-lg font-bold mb-4">Add a New Address</h3>

                <form method="POST" action="{{ route('profile.addresses.store') }}" class="space-y-4">
                    @csrf

                    <input type="text" name="address" placeholder="Address" required class="w-full border p-2 rounded" value="{{ old('address') }}">
                    <div class="flex space-x-4">
                        <input type="text" name="postal_code" placeholder="Postal Code" required class="w-1/3 border p-2 rounded" value="{{ old('postal_code') }}">
                        <input type="text" name="city" placeholder="City" required class="w-1/3 border p-2 rounded" value="{{ old('city') }}">
                        <input type="text" name="country" placeholder="Country" required class="w-1/3 border p-2 rounded" value="{{ old('country') }}">
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save Address</button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>