<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShopCatalogTest extends TestCase
{
    use RefreshDatabase;

    /** GD extension ছাড়াই চলে: আসল ১x১ PNG ফাইল */
    private function fakeImage(string $name = 'img.png'): UploadedFile
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');

        return UploadedFile::fake()->createWithContent($name, $png);
    }

    private function admin(): User
    {
        return User::create(['name' => 'A', 'email' => 'a@t.com', 'password' => 'password', 'role' => 'admin']);
    }

    private function owner(string $email = 'o@t.com'): User
    {
        return User::create(['name' => 'O', 'email' => $email, 'password' => 'password', 'role' => 'shop_owner']);
    }

    public function test_customer_sees_only_products_assigned_to_the_shop(): void
    {
        $owner = $this->owner();
        $shop = Shop::create(['name' => 'S1', 'owner_id' => $owner->id]);
        $assigned = Product::create(['name' => 'Visible Thing', 'price' => 10]);
        Product::create(['name' => 'Hidden Thing', 'price' => 10]); // কোনো shop-এ নেই
        $shop->products()->attach($assigned->id, ['price' => 15]);

        $this->get(route('shops.show', $shop))
            ->assertOk()
            ->assertSee('Visible Thing')
            ->assertDontSee('Hidden Thing');
    }

    public function test_customer_sees_shop_specific_price_and_details(): void
    {
        $o1 = $this->owner('o1@t.com');
        $o2 = $this->owner('o2@t.com');
        $s1 = Shop::create(['name' => 'S1', 'owner_id' => $o1->id]);
        $s2 = Shop::create(['name' => 'S2', 'owner_id' => $o2->id]);
        $p = Product::create(['name' => 'Gadget', 'price' => 100, 'description' => 'Base text']);

        $s1->products()->attach($p->id, ['price' => 111, 'details' => 'Only in shop one']);
        $s2->products()->attach($p->id, []); // কিছু override নেই → default

        $this->get(route('shops.show', $s1))->assertSee('111.00')->assertSee('Only in shop one');
        $this->get(route('shops.show', $s2))->assertSee('100.00')->assertSee('Base text')->assertDontSee('Only in shop one');
    }

    public function test_owner_edit_affects_only_own_shop(): void
    {
        Storage::fake('public');
        $o1 = $this->owner('o1@t.com');
        $o2 = $this->owner('o2@t.com');
        $s1 = Shop::create(['name' => 'S1', 'owner_id' => $o1->id]);
        $s2 = Shop::create(['name' => 'S2', 'owner_id' => $o2->id]);
        $p = Product::create(['name' => 'Gadget', 'price' => 100]);
        $s1->products()->attach($p->id, ['price' => 100]);
        $s2->products()->attach($p->id, ['price' => 100]);

        $this->actingAs($o1)->put(route('owner.shop-products.update', $p), [
            'price' => 250,
            'details' => 'Custom',
            'thumbnail' => $this->fakeImage('t.png'),
        ])->assertRedirect();

        $this->assertEquals(250, $s1->products()->first()->pivot->price);
        $this->assertEquals('Custom', $s1->products()->first()->pivot->details);
        $this->assertNotNull($s1->products()->first()->pivot->thumbnail);
        $this->assertEquals(100, $s2->products()->first()->pivot->price);
        $this->assertNull($s2->products()->first()->pivot->details);
        $this->assertEquals(100, (float) $p->fresh()->price);
    }

    public function test_owner_cannot_edit_product_not_in_their_shop(): void
    {
        $o1 = $this->owner('o1@t.com');
        $o2 = $this->owner('o2@t.com');
        Shop::create(['name' => 'S1', 'owner_id' => $o1->id]);
        $s2 = Shop::create(['name' => 'S2', 'owner_id' => $o2->id]);
        $p = Product::create(['name' => 'Gadget', 'price' => 100]);
        $s2->products()->attach($p->id);

        $this->actingAs($o1)->get(route('owner.shop-products.edit', $p))->assertNotFound();
    }

    public function test_owner_created_product_goes_to_catalog_and_own_shop(): void
    {
        $o = $this->owner();
        $shop = Shop::create(['name' => 'S1', 'owner_id' => $o->id]);

        $this->actingAs($o)->post(route('owner.products.store'), ['name' => 'Mine', 'price' => 20])->assertRedirect();

        $product = Product::where('name', 'Mine')->firstOrFail();
        $this->assertEquals($o->id, $product->created_by);
        $this->assertTrue($shop->products()->where('products.id', $product->id)->exists());

        // অন্য owner shared catalog-এ দেখে ও নিজের shop-এ add করতে পারে
        $o2 = $this->owner('o2@t.com');
        $shop2 = Shop::create(['name' => 'S2', 'owner_id' => $o2->id]);
        $this->actingAs($o2)->get(route('owner.catalog'))->assertSee('Mine');
        $this->actingAs($o2)->post(route('owner.catalog.add', $product))->assertRedirect();
        $this->assertTrue($shop2->products()->where('products.id', $product->id)->exists());
    }

    public function test_owner_cannot_edit_base_data_of_others_products(): void
    {
        $o1 = $this->owner('o1@t.com');
        Shop::create(['name' => 'S1', 'owner_id' => $o1->id]);
        $p = Product::create(['name' => 'Admin product', 'price' => 5, 'created_by' => $this->admin()->id]);

        $this->actingAs($o1)->put(route('owner.products.update', $p), ['name' => 'Hacked', 'price' => 1])->assertForbidden();
        $this->assertEquals('Admin product', $p->fresh()->name);
    }

    public function test_role_protection(): void
    {
        $admin = $this->admin();
        $owner = $this->owner();

        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->actingAs($owner)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($admin)->get(route('owner.dashboard'))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    }

    public function test_login_redirects_by_role(): void
    {
        $this->admin();
        $this->owner();

        $this->post('/login', ['email' => 'a@t.com', 'password' => 'password'])->assertRedirect(route('admin.dashboard'));
        $this->post('/logout');
        $this->post('/login', ['email' => 'o@t.com', 'password' => 'password'])->assertRedirect(route('owner.dashboard'));
        $this->post('/logout');
        $this->post('/login', ['email' => 'o@t.com', 'password' => 'wrong'])->assertSessionHasErrors('email');
    }

    public function test_admin_crud_for_shop_owner_shop_and_product(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.shops.store'), ['name' => 'New Shop'])->assertRedirect();
        $shop = Shop::where('name', 'New Shop')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.shop-owners.store'), [
            'name' => 'Owner X', 'email' => 'x@t.com', 'password' => 'secret123', 'password_confirmation' => 'secret123',
            'shop_id' => $shop->id,
        ])->assertRedirect();
        $owner = User::where('email', 'x@t.com')->firstOrFail();
        $this->assertEquals('shop_owner', $owner->role);
        $this->assertEquals($owner->id, $shop->fresh()->owner_id);

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'P', 'price' => 12.5, 'thumbnail' => $this->fakeImage('p.png'),
        ])->assertRedirect();
        $product = Product::where('name', 'P')->firstOrFail();
        $this->assertNotNull($product->thumbnail);

        $this->actingAs($admin)->put(route('admin.products.update', $product), ['name' => 'P2', 'price' => 1])->assertRedirect();
        $this->assertEquals('P2', $product->fresh()->name);

        $this->actingAs($admin)->delete(route('admin.products.destroy', $product))->assertRedirect();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);

        $this->actingAs($admin)->delete(route('admin.shop-owners.destroy', $owner))->assertRedirect();
        $this->assertNull($shop->fresh()->owner_id);

        $this->actingAs($admin)->delete(route('admin.shops.destroy', $shop))->assertRedirect();
        $this->assertDatabaseMissing('shops', ['id' => $shop->id]);
    }

    public function test_seeded_data_hides_unassigned_product(): void
    {
        Storage::fake('public');
        $this->seed();

        $this->assertDatabaseHas('products', ['name' => 'Unassigned Demo Product']);
        foreach (Shop::all() as $shop) {
            $this->get(route('shops.show', $shop))->assertOk()->assertDontSee('Unassigned Demo Product');
        }
        $this->get('/')->assertOk()->assertSee('Rafi Electronics');
    }
}