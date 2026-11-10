@extends('layouts.app')
@section('title', 'My Shop')

@section('content')
    @if (! $shop)
        <div class="card">
            <h1>My Shop</h1>
            <p>No shop is assigned to your account yet. Please contact the admin.</p>
        </div>
    @else
        <div class="toolbar">
            <div>
                <h1>{{ $shop->name }}</h1>
                <div class="muted">{{ $shop->description }}</div>
            </div>
            <div>
                <a class="btn" href="{{ route('owner.catalog') }}">Browse shared catalog</a>
                <a class="btn" href="{{ route('owner.products.create') }}">+ New product</a>
                <a class="btn secondary" href="{{ route('shops.show', $shop) }}">View as customer</a>
            </div>
        </div>

        <table>
            <thead><tr><th></th><th>Product</th><th>My price</th><th>My details</th><th></th></tr></thead>
            <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>
                        @if ($product->shopThumbnailUrl())
                            <img class="thumb-sm" src="{{ $product->shopThumbnailUrl() }}" alt="">
                        @else
                            <div class="thumb-sm noimg">–</div>
                        @endif
                    </td>
                    <td>{{ $product->name }}</td>
                    <td>৳{{ number_format((float) $product->shopPrice(), 2) }}</td>
                    <td class="muted">{{ \Illuminate\Support\Str::limit($product->shopDetails(), 70) }}</td>
                    <td class="actions">
                        <a class="btn small" href="{{ route('owner.shop-products.edit', $product) }}">Edit for my shop</a>
                        @if ($product->created_by === auth()->id())
                            <a class="btn small secondary" href="{{ route('owner.products.edit', $product) }}">Edit base product</a>
                        @endif
                        <form method="POST" action="{{ route('owner.shop-products.destroy', $product) }}" onsubmit="return confirm('Remove this product from your shop?')">
                            @csrf @method('DELETE')
                            <button class="btn small danger" type="submit">Remove</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Your shop has no products yet. Add some from the shared catalog or create your own.</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $products->links('pagination.simple') }}
    @endif
@endsection
