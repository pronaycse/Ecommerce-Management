<?php

namespace App\Http\Controllers\Owner;

class DashboardController extends OwnerController
{
    public function index()
    {
        $shop = auth()->user()->shop; // না থাকলেও dashboard খোলে, view message দেখায়

        $products = $shop
            ? $shop->products()->orderBy('products.name')->paginate(12)
            : null;

        return view('owner.dashboard', compact('shop', 'products'));
    }
}
