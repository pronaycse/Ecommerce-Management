@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
    <h1>Admin Dashboard</h1>

    <div class="stats">
        <div class="stat"><div class="n">{{ $shopCount }}</div><div class="muted">Shops</div></div>
        <div class="stat"><div class="n">{{ $productCount }}</div><div class="muted">Products in catalog</div></div>
        <div class="stat"><div class="n">{{ $ownerCount }}</div><div class="muted">Shop owners</div></div>
        <div class="stat"><div class="n">{{ $hiddenCount }}</div><div class="muted">Products not in any shop (hidden from customers)</div></div>
    </div>

    <div class="card">
        <a class="btn" href="{{ route('admin.shops.create') }}">+ New shop</a>
        <a class="btn" href="{{ route('admin.products.create') }}">+ New product</a>
        <a class="btn" href="{{ route('admin.shop-owners.create') }}">+ New shop owner</a>
    </div>
@endsection
