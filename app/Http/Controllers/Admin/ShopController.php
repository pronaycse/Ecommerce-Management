<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ShopController extends Controller
{
    public function index()
    {
        $shops = Shop::with('owner')->withCount('products')->latest()->paginate(10);

        return view('admin.shops.index', compact('shops'));
    }

    public function create()
    {
        return view('admin.shops.form', [
            'shop' => new Shop(),
            'owners' => $this->availableOwners(),
        ]);
    }

    public function store(Request $request)
    {
        Shop::create($this->validated($request));

        return redirect()->route('admin.shops.index')->with('success', 'Shop created.');
    }

    public function edit(Shop $shop)
    {
        return view('admin.shops.form', [
            'shop' => $shop,
            'owners' => $this->availableOwners($shop),
        ]);
    }

    public function update(Request $request, Shop $shop)
    {
        $shop->update($this->validated($request, $shop));

        return redirect()->route('admin.shops.index')->with('success', 'Shop updated.');
    }

    public function destroy(Shop $shop)
    {
        // shop-specific thumbnail ফাইলগুলোও মুছি (pivot row cascade দিয়ে নিজে মুছবে)
        foreach ($shop->products()->get() as $product) {
            if ($product->pivot->thumbnail) {
                Storage::disk('public')->delete($product->pivot->thumbnail);
            }
        }
        $shop->delete();

        return redirect()->route('admin.shops.index')->with('success', 'Shop deleted.');
    }

    private function validated(Request $request, ?Shop $shop = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'owner_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('role', User::ROLE_SHOP_OWNER),
                Rule::unique('shops', 'owner_id')->ignore($shop?->id),
            ],
        ]);
    }

    /** যেসব owner-এর এখনো shop নেই (edit-এর সময় বর্তমান owner-ও থাকে) */
    private function availableOwners(?Shop $shop = null)
    {
        return User::where('role', User::ROLE_SHOP_OWNER)
            ->where(function ($q) use ($shop) {
                $q->whereDoesntHave('shop');
                if ($shop && $shop->owner_id) {
                    $q->orWhere('id', $shop->owner_id);
                }
            })
            ->orderBy('name')
            ->get();
    }
}
