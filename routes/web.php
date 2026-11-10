<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Customer;
use App\Http\Controllers\Owner;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customer (public) — login লাগে না
|--------------------------------------------------------------------------
*/
Route::get('/', [Customer\HomeController::class, 'index'])->name('home');
Route::get('/shops/{shop}', [Customer\ShopController::class, 'show'])->name('shops.show');

/*
|--------------------------------------------------------------------------
| Authentication (register নেই — account admin বানিয়ে দেয়)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', RoleMiddleware::class . ':admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('shops', Admin\ShopController::class)->except('show');
        Route::resource('products', Admin\ProductController::class)->except('show');
        Route::resource('shop-owners', Admin\ShopOwnerController::class)
            ->except('show')
            ->parameters(['shop-owners' => 'owner']);
    });

/*
|--------------------------------------------------------------------------
| Shop owner panel
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', RoleMiddleware::class . ':shop_owner'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        Route::get('/', [Owner\DashboardController::class, 'index'])->name('dashboard');

        // Shared catalog
        Route::get('/catalog', [Owner\CatalogController::class, 'index'])->name('catalog');
        Route::post('/catalog/{product}/add', [Owner\CatalogController::class, 'add'])->name('catalog.add');

        // নিজের product তৈরি/edit
        Route::get('/products/create', [Owner\ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [Owner\ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [Owner\ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [Owner\ProductController::class, 'update'])->name('products.update');

        // নিজের shop-এর জন্য price/details/thumbnail
        Route::get('/shop-products/{product}/edit', [Owner\ShopProductController::class, 'edit'])->name('shop-products.edit');
        Route::put('/shop-products/{product}', [Owner\ShopProductController::class, 'update'])->name('shop-products.update');
        Route::delete('/shop-products/{product}', [Owner\ShopProductController::class, 'destroy'])->name('shop-products.destroy');
    });
