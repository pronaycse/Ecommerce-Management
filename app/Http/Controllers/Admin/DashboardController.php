<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'shopCount' => Shop::count(),
            'productCount' => Product::count(),
            'ownerCount' => User::where('role', User::ROLE_SHOP_OWNER)->count(),
            'hiddenCount' => Product::doesntHave('shops')->count(),
        ]);
    }
}
