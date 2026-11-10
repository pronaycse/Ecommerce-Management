<?php

namespace App\Http\Controllers\Owner;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends OwnerController
{
    public function create()
    {
        $this->shop();

        return view('owner.products.form', ['product' => new Product()]);
    }

    public function store(Request $request)
    {
        $shop = $this->shop();
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        }
        unset($data['remove_thumbnail']);

        $product = Product::create($data); // shared catalog-এ চলে গেল

        // নিজের shop-এও অটো add
        $shop->products()->attach($product->id, ['price' => $product->price]);

        return redirect()->route('owner.dashboard')
            ->with('success', 'Product created. It is now in the shared catalog and in your shop.');
    }

    public function edit(Product $product)
    {
        $this->shop();
        $this->authorizeOwn($product);

        return view('owner.products.form', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->shop();
        $this->authorizeOwn($product);

        $data = $this->validated($request);

        if ($request->hasFile('thumbnail')) {
            $this->deleteFile($product->thumbnail);
            $data['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        } elseif ($request->boolean('remove_thumbnail')) {
            $this->deleteFile($product->thumbnail);
            $data['thumbnail'] = null;
        }
        unset($data['remove_thumbnail']);

        $product->update($data);

        return redirect()->route('owner.dashboard')->with('success', 'Product updated.');
    }

    /** owner শুধু নিজের বানানো product-এর মূল তথ্য বদলাতে পারে */
    private function authorizeOwn(Product $product): void
    {
        abort_unless($product->created_by === auth()->id(), 403, 'You can only edit products you created.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
            'remove_thumbnail' => ['nullable', 'boolean'],
        ]);
    }
}
