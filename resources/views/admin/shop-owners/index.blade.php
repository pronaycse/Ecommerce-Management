@extends('layouts.app')
@section('title', 'Shop Owners')

@section('content')
    <div class="toolbar">
        <h1>Shop owners</h1>
        <a class="btn" href="{{ route('admin.shop-owners.create') }}">+ New shop owner</a>
    </div>

    <table>
        <thead><tr><th>Name</th><th>Email</th><th>Shop</th><th></th></tr></thead>
        <tbody>
        @forelse ($owners as $owner)
            <tr>
                <td>{{ $owner->name }}</td>
                <td>{{ $owner->email }}</td>
                <td>{{ $owner->shop?->name ?? '— no shop —' }}</td>
                <td class="actions">
                    <a class="btn small secondary" href="{{ route('admin.shop-owners.edit', $owner) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.shop-owners.destroy', $owner) }}" onsubmit="return confirm('Delete this account? Their shop will remain without an owner.')">
                        @csrf @method('DELETE')
                        <button class="btn small danger" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4">No shop owners yet.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $owners->links('pagination.simple') }}
@endsection
