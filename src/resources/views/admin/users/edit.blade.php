@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-4">✏️ Edit role for {{ $user->name }}</h1>

<form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-4">
    @csrf
    @method('PUT')

    <div>
        <label class="block font-semibold">Role</label>
        <select name="role" class="border rounded w-full p-2">
            <option value="customer" @if($user->role === 'customer') selected @endif>Customer</option>
            <option value="admin" @if($user->role === 'admin') selected @endif>Admin</option>
        </select>
    </div>

    <div>
        <button class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
    </div>
</form>
@endsection
