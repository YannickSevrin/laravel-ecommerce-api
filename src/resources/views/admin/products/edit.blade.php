@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="text-xl font-bold mb-4">Edit product</h1>
    @include('admin.products.form', [
        'route' => route('admin.products.update', $product),
        'method' => 'PUT',
        'product' => $product
    ])
</div>
@endsection
