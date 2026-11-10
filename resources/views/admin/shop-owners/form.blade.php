@extends('layouts.app')
@section('title', $owner->exists ? 'Edit Shop Owner' : 'New Shop Owner')

@section('content')
    <h1>{{ $owner->exists ? 'Edit shop owner' : 'New shop owner' }}</h1>

    <form method="POST" action="{{ $owner->exists ? route('admin.shop-owners.update', $owner) : route('admin.shop-owners.store') }}" class="card form wide-form">
        @csrf
        @if ($owner->exists) @method('PUT') @endif

        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $owner->name) }}" required>
        @error('name') <div class="error">{{ $message }}</div> @enderror

        <label for="email">Email (login)</label>
        <input type="email" id="email" name="email" value="{{ old('email', $owner->email) }}" required>
        @error('email') <div class="error">{{ $message }}</div> @enderror

        <label for="password">Password {{ $owner->exists ? '(leave blank to keep current)' : '' }}</label>
        <input type="password" id="password" name="password" {{ $owner->exists ? '' : 'required' }}>
        @error('password') <div class="error">{{ $message }}</div> @enderror

        <label for="password_confirmation">Confirm password</label>
        <input type="password" id="password_confirmation" name="password_confirmation">

        <label for="shop_id">Shop</label>
        <select id="shop_id" name="shop_id">
            <option value="">— none —</option>
            @foreach ($shops as $shop)
                <option value="{{ $shop->id }}" @selected(old('shop_id', $owner->shop?->id) == $shop->id)>{{ $shop->name }}</option>
            @endforeach
        </select>
        <div class="muted">Only shops without an owner are listed.</div>
        @error('shop_id') <div class="error">{{ $message }}</div> @enderror

        <div class="buttons">
            <button type="submit" class="btn">{{ $owner->exists ? 'Update owner' : 'Create owner' }}</button>
            <a href="{{ route('admin.shop-owners.index') }}" class="btn secondary">Cancel</a>
        </div>
    </form>
@endsection
