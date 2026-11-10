<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Support\Facades\Storage;

/** Owner panel-এর সব controller-এর base: logged-in owner-এর shop বের করে */
abstract class OwnerController extends Controller
{
    protected function shop(): Shop
    {
        $shop = auth()->user()->shop;

        abort_unless($shop, 403, 'No shop is assigned to your account yet. Please contact the admin.');

        return $shop;
    }

    protected function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
