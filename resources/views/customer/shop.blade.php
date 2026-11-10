@extends('layouts.app')
@section('title', $shop->name)

@section('content')
    <p><a href="{{ route('home') }}">&laquo; All shops</a></p>
    <h1>{{ $shop->name }}</h1>
    @if ($shop->description)
        <p class="muted">{{ $shop->description }}</p>
    @endif

    @if ($products->isEmpty())
        <div class="card">This shop has no products yet.</div>
    @else
        <div class="grid">
            @foreach ($products as $product)
                <div class="product">
                    @if ($product->shopThumbnailUrl())
                        <img class="thumb" src="{{ $product->shopThumbnailUrl() }}" alt="{{ $product->name }}">
                    @else
                        <div class="thumb noimg">No image</div>
                    @endif
                    <div class="body">
                        <h3>{{ $product->name }}</h3>
                        <div class="price">৳{{ number_format((float) $product->shopPrice(), 2) }}</div>
                        <div class="muted">{!! nl2br(e($product->shopDetails())) !!}</div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $products->links('pagination.simple') }}
    @endif
@endsection
