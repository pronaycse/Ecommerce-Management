@extends('layouts.app')
@section('title', $product->exists ? 'Edit Product' : 'New Product')

@section('content')
    <h1>{{ $product->exists ? 'Edit product' : 'New product' }}</h1>

    @include('partials.product-form', [
        'product' => $product,
        'action' => $product->exists ? route('admin.products.update', $product) : route('admin.products.store'),
        'cancel' => route('admin.products.index'),
    ])
@endsection
