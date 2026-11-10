<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Admin ----
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
        ]);

        // ---- Shop owners + তাদের shop ----
        $shops = [];
        foreach ([
            ['Rafi Karim', 'owner1@example.com', 'Rafi Electronics', 'Gadgets and accessories at fair prices.'],
            ['Nila Akter', 'owner2@example.com', 'Nila Fashion House', 'Everyday fashion and lifestyle items.'],
            ['Sajid Hasan', 'owner3@example.com', 'Sajid Grocery', 'Fresh and packaged groceries.'],
        ] as [$ownerName, $email, $shopName, $desc]) {
            $owner = User::create([
                'name' => $ownerName,
                'email' => $email,
                'password' => 'password',
                'role' => User::ROLE_SHOP_OWNER,
            ]);
            $shops[$email] = [
                'owner' => $owner,
                'shop' => Shop::create(['name' => $shopName, 'description' => $desc, 'owner_id' => $owner->id]),
            ];
        }
        $s1 = $shops['owner1@example.com'];
        $s2 = $shops['owner2@example.com'];
        $s3 = $shops['owner3@example.com'];

        // ---- Shared catalog: admin-এর product ----
        $headphones = $this->product('Wireless Headphones', 'Over-ear Bluetooth headphones.', 3500, $admin, '#2563eb', 'headphones');
        $watch = $this->product('Smart Watch', 'Fitness tracking smart watch.', 4800, $admin, '#7c3aed', 'watch');
        $tshirt = $this->product('Cotton T-Shirt', 'Soft 100% cotton t-shirt.', 550, $admin, '#db2777', 'tshirt');
        $rice = $this->product('Basmati Rice 5kg', 'Premium long grain rice.', 900, $admin, '#d97706', 'rice');
        // কোনো shop-এ assign করা নেই → customer কখনো দেখবে না
        $this->product('Unassigned Demo Product', 'Not in any shop, so customers cannot see it.', 100, $admin, '#6b7280', 'box');

        // ---- Shared catalog: shop owner-এর নিজের product (নিজের shop-এও যায়) ----
        $powerbank = $this->product('Power Bank 10000mAh', 'Fast-charging power bank.', 1800, $s1['owner'], '#059669', 'powerbank');
        $s1['shop']->products()->attach($powerbank->id, ['price' => 1800]);

        $panjabi = $this->product('Panjabi', 'Festival panjabi, embroidered.', 1500, $s2['owner'], '#be123c', 'panjabi');
        $s2['shop']->products()->attach($panjabi->id, ['price' => 1500]);

        // ---- Shared product shop-এ add + shop-ভিত্তিক price/details/thumbnail ----
        $s1['shop']->products()->attach($headphones->id, ['price' => 3200, 'details' => 'Rafi Electronics offer: 1 year warranty included.']);
        $s1['shop']->products()->attach($watch->id, ['price' => 4800]);

        // একই headphones অন্য shop-এ ভিন্ন দামে (shop-wise price প্রমাণ)
        $s2['shop']->products()->attach($headphones->id, ['price' => 3600]);
        $s2['shop']->products()->attach($tshirt->id, [
            'price' => 520,
            'details' => 'Available in M, L, XL.',
            'thumbnail' => $this->svg('Nila T-Shirt', 'tshirt', '#0ea5e9'),
        ]);

        $s3['shop']->products()->attach($rice->id, ['price' => 880]);
        $s3['shop']->products()->attach($powerbank->id, ['price' => 1750]);
    }

    private function product(string $name, string $description, float $price, User $creator, string $color, string $kind): Product
    {
        return Product::create([
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'thumbnail' => $this->svg($name, $kind, $color),
            'created_by' => $creator->id,
        ]);
    }

    /** demo ছবি (নিজে আঁকা SVG) storage/app/public/products-এ রাখে */
    private function svg(string $name, string $kind, string $color): string
    {
        $path = 'products/seed-' . Str::slug($name) . '.svg';
        Storage::disk('public')->put($path, ProductArt::svg($kind, $color, $name));

        return $path;
    }
}