<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('creator')->withCount('shops')->latest()->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.form', ['product' => new Product()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        }
        unset($data['remove_thumbnail']);

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product created and added to the shared catalog.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
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

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        // default thumbnail + সব shop-এর নিজস্ব thumbnail ফাইল মুছি
        $this->deleteFile($product->thumbnail);
        DB::table('shop_product')->where('product_id', $product->id)->pluck('thumbnail')
            ->each(fn ($path) => $this->deleteFile($path));

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
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

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
