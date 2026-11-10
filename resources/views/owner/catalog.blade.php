@extends('layouts.app')
@section('title', 'Shared Catalog')

@section('content')
    <div class="toolbar">
        <h1>Shared catalog</h1>
        <form method="GET" action="{{ route('owner.catalog') }}">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products…" style="padding:7px 10px;border:1px solid #d1d5db;border-radius:6px;">
            <button class="btn" type="submit">Search</button>
        </form>
    </div>

    @if ($products->isEmpty())
        <div class="card">No products found.</div>
    @else
        <div class="grid">
            @foreach ($products as $product)
                <div class="product">
                    @if ($product->thumbnailUrl())
                        <img class="thumb" src="{{ $product->thumbnailUrl() }}" alt="{{ $product->name }}">
                    @else
                        <div class="thumb noimg">No image</div>
                    @endif
                    <div class="body">
                        <h3>{{ $product->name }}</h3>
                        <div class="price">৳{{ number_format((float) $product->price, 2) }}</div>
                        <div class="muted">{{ \Illuminate\Support\Str::limit($product->description, 80) }}</div>
                        <div class="muted">By {{ $product->creator?->name ?? 'unknown' }}</div>
                        <div style="margin-top:auto">
                            @if (in_array($product->id, $inShop))
                                <span class="badge green">In your shop</span>
                            @else
                                <form method="POST" action="{{ route('owner.catalog.add', $product) }}">
                                    @csrf
                                    <button class="btn small" type="submit">Add to my shop</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $products->links('pagination.simple') }}
    @endif
@endsection
