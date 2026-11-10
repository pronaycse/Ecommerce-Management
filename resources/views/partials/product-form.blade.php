{{-- ব্যবহার: @include('partials.product-form', ['product' => $product, 'action' => url, 'cancel' => url]) --}}
<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="card form wide-form">
    @csrf
    @if ($product->exists)
        @method('PUT')
    @endif

    <label for="name">Name</label>
    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required>
    @error('name') <div class="error">{{ $message }}</div> @enderror

    <label for="price">Default price (৳)</label>
    <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price', $product->price) }}" required>
    @error('price') <div class="error">{{ $message }}</div> @enderror

    <label for="description">Description</label>
    <textarea id="description" name="description">{{ old('description', $product->description) }}</textarea>
    @error('description') <div class="error">{{ $message }}</div> @enderror

    <label for="thumbnail">Default thumbnail (jpg, png, webp, gif — max 2MB)</label>
    @if ($product->thumbnailUrl())
        <p><img class="thumb-sm" src="{{ $product->thumbnailUrl() }}" alt=""></p>
    @endif
    <input type="file" id="thumbnail" name="thumbnail" accept="image/*">
    @error('thumbnail') <div class="error">{{ $message }}</div> @enderror
    @if ($product->thumbnail)
        <label class="check"><input type="checkbox" name="remove_thumbnail" value="1"> Remove current thumbnail</label>
    @endif

    <div class="buttons">
        <button type="submit" class="btn">{{ $product->exists ? 'Update product' : 'Create product' }}</button>
        <a href="{{ $cancel }}" class="btn secondary">Cancel</a>
    </div>
</form>
