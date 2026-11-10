@extends('layouts.app')
@section('title', 'Manage Products')

@section('content')
    <div class="toolbar">
        <h1>Products (shared catalog)</h1>
        <a class="btn" href="{{ route('admin.products.create') }}">+ New product</a>
    </div>

    <table>
        <thead><tr><th></th><th>Name</th><th>Default price</th><th>Created by</th><th>In shops</th><th></th></tr></thead>
        <tbody>
        @forelse ($products as $product)
            <tr>
                <td>
                    @if ($product->thumbnailUrl())
                        <img class="thumb-sm" src="{{ $product->thumbnailUrl() }}" alt="">
                    @else
                        <div class="thumb-sm noimg">–</div>
                    @endif
                </td>
                <td>{{ $product->name }}</td>
                <td>৳{{ number_format((float) $product->price, 2) }}</td>
                <td>{{ $product->creator?->name ?? '—' }}</td>
                <td>
                    @if ($product->shops_count > 0)
                        <span class="badge green">{{ $product->shops_count }}</span>
                    @else
                        <span class="badge">hidden</span>
                    @endif
                </td>
                <td class="actions">
                    <a class="btn small secondary" href="{{ route('admin.products.edit', $product) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product from the catalog and all shops?')">
                        @csrf @method('DELETE')
                        <button class="btn small danger" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6">No products yet.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $products->links('pagination.simple') }}
@endsection
