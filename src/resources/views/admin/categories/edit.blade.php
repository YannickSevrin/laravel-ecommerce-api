@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-4">✏️ Edit category</h1>

@include('admin.categories.form', [
    'route' => route('admin.categories.update', $category),
    'method' => 'PUT',
    'category' => $category
])
@endsection
