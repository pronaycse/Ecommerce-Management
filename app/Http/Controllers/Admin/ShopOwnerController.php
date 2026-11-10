<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ShopOwnerController extends Controller
{
    public function index()
    {
        $owners = User::where('role', User::ROLE_SHOP_OWNER)->with('shop')->latest()->paginate(10);

        return view('admin.shop-owners.index', compact('owners'));
    }

    public function create()
    {
        return view('admin.shop-owners.form', [
            'owner' => new User(),
            'shops' => $this->availableShops(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'shop_id' => ['nullable', Rule::exists('shops', 'id')->whereNull('owner_id')],
        ]);

        DB::transaction(function () use ($data) {
            $owner = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'], // User model-এর 'hashed' cast নিজে hash করে
                'role' => User::ROLE_SHOP_OWNER,
            ]);

            if (! empty($data['shop_id'])) {
                Shop::whereKey($data['shop_id'])->update(['owner_id' => $owner->id]);
            }
        });

        return redirect()->route('admin.shop-owners.index')->with('success', 'Shop owner account created.');
    }

    public function edit(User $owner)
    {
        $this->ensureOwner($owner);

        return view('admin.shop-owners.form', [
            'owner' => $owner,
            'shops' => $this->availableShops($owner),
        ]);
    }

    public function update(Request $request, User $owner)
    {
        $this->ensureOwner($owner);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($owner->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'shop_id' => [
                'nullable',
                Rule::exists('shops', 'id')->where(
                    fn ($q) => $q->whereNull('owner_id')->orWhere('owner_id', $owner->id)
                ),
            ],
        ]);

        DB::transaction(function () use ($data, $owner) {
            $owner->name = $data['name'];
            $owner->email = $data['email'];
            if (! empty($data['password'])) {
                $owner->password = $data['password'];
            }
            $owner->save();

            // আগের shop থেকে সরিয়ে নতুন shop (থাকলে) link করি
            Shop::where('owner_id', $owner->id)->update(['owner_id' => null]);
            if (! empty($data['shop_id'])) {
                Shop::whereKey($data['shop_id'])->update(['owner_id' => $owner->id]);
            }
        });

        return redirect()->route('admin.shop-owners.index')->with('success', 'Shop owner updated.');
    }

    public function destroy(User $owner)
    {
        $this->ensureOwner($owner);
        $owner->delete(); // shop-এর owner_id null হয়ে যায়, shop থেকে যায়

        return redirect()->route('admin.shop-owners.index')->with('success', 'Shop owner deleted.');
    }

    private function ensureOwner(User $user): void
    {
        abort_unless($user->role === User::ROLE_SHOP_OWNER, 404);
    }

    /** owner নেই এমন shop (edit-এর সময় এই owner-এর বর্তমান shop-ও থাকে) */
    private function availableShops(?User $owner = null)
    {
        return Shop::where(function ($q) use ($owner) {
            $q->whereNull('owner_id');
            if ($owner && $owner->exists) {
                $q->orWhere('owner_id', $owner->id);
            }
        })->orderBy('name')->get();
    }
}
