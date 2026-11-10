<?php

namespace App\Http\Controllers\Owner;

use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends OwnerController
{
    public function index(Request $request)
    {
        $shop = $this->shop();

        $products = Product::with('creator')
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%' . $request->q . '%'))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        // এই shop-এ আগে থেকেই যেগুলো আছে তাদের id
        $inShop = $shop->products()->pluck('products.id')->all();

        return view('owner.catalog', compact('shop', 'products', 'inShop'));
    }

    public function add(Product $product)
    {
        $shop = $this->shop();

        // শুরুতে shop-এর price = product-এর default price; পরে owner বদলাতে পারবে
        $shop->products()->syncWithoutDetaching([
            $product->id => ['price' => $product->price],
        ]);

        return back()->with('success', '"' . $product->name . '" added to your shop.');
    }
}
