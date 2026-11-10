<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Shop;

class HomeController extends Controller
{
    public function index()
    {
        $shops = Shop::withCount('products')->orderBy('name')->get();

        return view('customer.home', compact('shops'));
    }
}
