@extends('layouts.app')
@section('title', $product->exists ? 'Edit Product' : 'New Product')

@section('content')
    <h1>{{ $product->exists ? 'Edit my product' : 'Create a new product' }}</h1>
    @unless ($product->exists)
        <p class="muted">New products are added to the shared catalog (other owners can use them) and to your shop automatically.</p>
    @endunless

    @include('partials.product-form', [
        'product' => $product,
        'action' => $product->exists ? route('owner.products.update', $product) : route('owner.products.store'),
        'cancel' => route('owner.dashboard'),
    ])
@endsection
