<x-admin-layout title="Create Category">
    <h1 class="text-2xl font-bold mb-4">➕ Create a category</h1>

    @include('admin.categories.form', [
        'route' => route('admin.categories.store'),
        'method' => 'POST',
        'category' => null
    ])
</x-admin-layout>
