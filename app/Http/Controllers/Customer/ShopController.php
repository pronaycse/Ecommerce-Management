<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Shop;

class ShopController extends Controller
{
    public function show(Shop $shop)
    {
        // শুধু এই shop-এ assign করা product (pivot) — assign না থাকলে কখনো আসবে না
        $products = $shop->products()->orderBy('products.name')->paginate(12);

        return view('customer.shop', compact('shop', 'products'));
    }
}
