@extends('layouts.app')
@section('title', 'Manage Shops')

@section('content')
    <div class="toolbar">
        <h1>Shops</h1>
        <a class="btn" href="{{ route('admin.shops.create') }}">+ New shop</a>
    </div>

    <table>
        <thead><tr><th>Name</th><th>Owner</th><th>Products</th><th></th></tr></thead>
        <tbody>
        @forelse ($shops as $shop)
            <tr>
                <td><a href="{{ route('shops.show', $shop) }}">{{ $shop->name }}</a></td>
                <td>{{ $shop->owner?->name ?? '— no owner —' }}</td>
                <td>{{ $shop->products_count }}</td>
                <td class="actions">
                    <a class="btn small secondary" href="{{ route('admin.shops.edit', $shop) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.shops.destroy', $shop) }}" onsubmit="return confirm('Delete this shop?')">
                        @csrf @method('DELETE')
                        <button class="btn small danger" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4">No shops yet.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $shops->links('pagination.simple') }}
@endsection
