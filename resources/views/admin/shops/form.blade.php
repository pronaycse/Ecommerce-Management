@extends('layouts.app')
@section('title', $shop->exists ? 'Edit Shop' : 'New Shop')

@section('content')
    <h1>{{ $shop->exists ? 'Edit shop' : 'New shop' }}</h1>

    <form method="POST" action="{{ $shop->exists ? route('admin.shops.update', $shop) : route('admin.shops.store') }}" class="card form wide-form">
        @csrf
        @if ($shop->exists) @method('PUT') @endif

        <label for="name">Shop name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $shop->name) }}" required>
        @error('name') <div class="error">{{ $message }}</div> @enderror

        <label for="description">Description</label>
        <textarea id="description" name="description">{{ old('description', $shop->description) }}</textarea>
        @error('description') <div class="error">{{ $message }}</div> @enderror

        <label for="owner_id">Shop owner</label>
        <select id="owner_id" name="owner_id">
            <option value="">— none —</option>
            @foreach ($owners as $owner)
                <option value="{{ $owner->id }}" @selected(old('owner_id', $shop->owner_id) == $owner->id)>{{ $owner->name }} ({{ $owner->email }})</option>
            @endforeach
        </select>
        <div class="muted">Only owners without a shop are listed. Create owners from "Shop Owners".</div>
        @error('owner_id') <div class="error">{{ $message }}</div> @enderror

        <div class="buttons">
            <button type="submit" class="btn">{{ $shop->exists ? 'Update shop' : 'Create shop' }}</button>
            <a href="{{ route('admin.shops.index') }}" class="btn secondary">Cancel</a>
        </div>
    </form>
@endsection
