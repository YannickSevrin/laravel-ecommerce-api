@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-4">👥 Users</h1>

@if (session('success'))
    <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<table class="w-full text-left border-collapse">
    <thead>
        <tr>
            <th class="border px-4 py-2">Name</th>
            <th class="border px-4 py-2">Email</th>
            <th class="border px-4 py-2">Role</th>
            <th class="border px-4 py-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr>
                <td class="border px-4 py-2">{{ $user->name }}</td>
                <td class="border px-4 py-2">{{ $user->email }}</td>
                <td class="border px-4 py-2">{{ ucfirst($user->role) }}</td>
                <td class="border px-4 py-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:underline">Edit</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
