@extends('layouts.app')
@section('title', 'Edit for my shop')

@section('content')
    <h1>{{ $product->name }} — for my shop</h1>
    <p class="muted">These values apply only to your shop. Leave details empty to use the product's default description.</p>

    <form method="POST" action="{{ route('owner.shop-products.update', $product) }}" enctype="multipart/form-data" class="card form wide-form">
        @csrf @method('PUT')

        <label for="price">My price (৳) — default is ৳{{ number_format((float) $product->price, 2) }}</label>
        <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price', $product->shopPrice()) }}" required>
        @error('price') <div class="error">{{ $message }}</div> @enderror

        <label for="details">My details</label>
        <textarea id="details" name="details" placeholder="{{ $product->description }}">{{ old('details', $product->pivot->details) }}</textarea>
        @error('details') <div class="error">{{ $message }}</div> @enderror

        <label for="thumbnail">My thumbnail</label>
        @if ($product->shopThumbnailUrl())
            <p><img class="thumb-sm" src="{{ $product->shopThumbnailUrl() }}" alt=""></p>
        @endif
        <input type="file" id="thumbnail" name="thumbnail" accept="image/*">
        @error('thumbnail') <div class="error">{{ $message }}</div> @enderror
        @if ($product->pivot->thumbnail)
            <label class="check"><input type="checkbox" name="remove_thumbnail" value="1"> Remove my thumbnail (use the default one)</label>
        @endif

        <div class="buttons">
            <button type="submit" class="btn">Save</button>
            <a href="{{ route('owner.dashboard') }}" class="btn secondary">Cancel</a>
        </div>
    </form>
@endsection
