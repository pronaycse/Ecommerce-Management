<?php

namespace App\Http\Controllers\Owner;

use App\Models\Product;
use Illuminate\Http\Request;

/** নিজের shop-এ থাকা product-এর shop-ভিত্তিক price / details / thumbnail */
class ShopProductController extends OwnerController
{
    public function edit(Product $product)
    {
        $item = $this->findInShop($product);

        return view('owner.shop-products.edit', ['product' => $item]);
    }

    public function update(Request $request, Product $product)
    {
        $item = $this->findInShop($product);

        $data = $request->validate([
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'details' => ['nullable', 'string', 'max:5000'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
            'remove_thumbnail' => ['nullable', 'boolean'],
        ]);

        $pivot = [
            'price' => $data['price'],
            'details' => $data['details'] ?? null,
        ];

        if ($request->hasFile('thumbnail')) {
            $this->deleteFile($item->pivot->thumbnail);
            $pivot['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        } elseif ($request->boolean('remove_thumbnail')) {
            $this->deleteFile($item->pivot->thumbnail);
            $pivot['thumbnail'] = null;
        }

        $this->shop()->products()->updateExistingPivot($product->id, $pivot);

        return redirect()->route('owner.dashboard')->with('success', 'Shop product updated.');
    }

    public function destroy(Product $product)
    {
        $item = $this->findInShop($product);

        $this->deleteFile($item->pivot->thumbnail);
        $this->shop()->products()->detach($product->id);

        return redirect()->route('owner.dashboard')->with('success', 'Product removed from your shop.');
    }

    /** product যদি এই owner-এর shop-এ না থাকে → 404 */
    private function findInShop(Product $product): Product
    {
        return $this->shop()->products()->where('products.id', $product->id)->firstOrFail();
    }
}
