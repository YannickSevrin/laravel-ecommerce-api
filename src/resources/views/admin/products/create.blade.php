<x-admin-layout title="Create Product">
    <div class="container">
        <h1 class="text-xl font-bold mb-4">Create a product</h1>
        @include('admin.products.form', [
            'route' => route('admin.products.store'),
            'method' => 'POST',
            'product' => null
        ])
    </div>
</x-admin-layout>
