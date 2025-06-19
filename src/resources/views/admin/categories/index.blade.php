@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-4">📁 Categories</h1>

<a href="{{ route('admin.categories.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">➕ Create a category</a>

@if (session('success'))
    <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<table class="w-full text-left border-collapse">
    <thead>
        <tr>
            <th class="border px-4 py-2">Name</th>
            <th class="border px-4 py-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($categories as $category)
            <tr>
                <td class="border px-4 py-2">{{ $category->name }}</td>
                <td class="border px-4 py-2">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-600 hover:underline">Edit</a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline-block ml-2">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Delete this category?')" class="text-red-600 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
