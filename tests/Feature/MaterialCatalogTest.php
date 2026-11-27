<?php

namespace Tests\Feature;

use App\Livewire\MaterialCatalog;
use App\Models\Material;
use App\Models\Order;
use Database\Seeders\MaterialSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MaterialCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(MaterialSeeder::class);
    }

    public function test_material_catalog_component_renders_grouped_materials(): void
    {
        Livewire::test(MaterialCatalog::class)
            ->assertSee('Daftar Harga Transparan')
            ->assertSee('Kategori: Daging')
            ->assertSee('Kategori: Tepung')
            ->assertSee('Kategori: Bumbu')
            ->assertSee('Daging Sapi')
            ->assertSee('Daging Ayam')
            ->assertSee('Tepung Tapioka')
            ->assertSee('Bawang Putih Kupas')
            ->assertSee('Garam Kasar')
            ->assertSee('Jasa Selepan (Penggilingan)');
    }

    public function test_welcome_page_contains_material_catalog(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="daftar-harga"', false);
        $response->assertSee('Daftar Harga Transparan');
    }

    public function test_can_increment_and_decrement_cart_items_without_redirect(): void
    {
        $material = Material::where('name', 'Tepung Tapioka')->first();

        Livewire::test(MaterialCatalog::class)
            ->call('incrementItem', $material->id)
            ->assertSet('selectedItems.'.$material->id, 1)
            ->assertSee('Keranjang Belanja Bahan')
            ->call('incrementItem', $material->id)
            ->assertSet('selectedItems.'.$material->id, 2)
            ->call('decrementItem', $material->id)
            ->assertSet('selectedItems.'.$material->id, 1)
            ->call('decrementItem', $material->id)
            ->assertSet('selectedItems', []);
    }

    public function test_can_filter_materials_by_category(): void
    {
        Livewire::test(MaterialCatalog::class)
            ->set('activeCategory', 'tepung')
            ->assertSee('Tepung Tapioka')
            ->assertDontSee('Daging Sapi');
    }

    public function test_can_search_materials(): void
    {
        Livewire::test(MaterialCatalog::class)
            ->set('search', 'merica')
            ->assertSee('Merica Bubuk Sachet')
            ->assertDontSee('Daging Sapi');
    }

    public function test_transfer_to_calculator_stores_session(): void
    {
        $material = Material::where('name', 'Merica Bubuk Sachet')->first();

        Livewire::test(MaterialCatalog::class)
            ->call('incrementItem', $material->id)
            ->call('transferToCalculator')
            ->assertSessionHas('selected_materials', [$material->id => 1])
            ->assertRedirect(route('calculator'));
    }

    public function test_direct_checkout_creates_order(): void
    {
        $material = Material::where('name', 'Tepung Tapioka')->first();
        $pickup = now()->addDay()->setTime(8, 0)->format('Y-m-d\TH:i');

        $test = Livewire::test(MaterialCatalog::class)
            ->call('incrementItem', $material->id)
            ->set('customerName', 'Siti Rahma')
            ->set('customerPhone', '08123456789')
            ->set('pickupTime', $pickup)
            ->call('processDirectCheckout')
            ->assertHasNoErrors()
            ->assertSet('showDirectCheckoutModal', true)
            ->assertSet('selectedItems', [])
            ->assertSee('Pesanan Berhasil Dicatat');

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Siti Rahma',
            'customer_phone' => '08123456789',
            'start_type' => 'ingredients_only',
        ]);

        $orderCode = (string) $test->get('confirmedOrderCode');
        $this->assertStringStartsWith('#BSK-', $orderCode);
        $this->assertStringContainsString(urlencode($orderCode), (string) $test->get('confirmedWaUrl'));
        $this->assertStringContainsString('/lacak-pesanan', (string) $test->get('confirmedTrackUrl'));

        // Anti-double-order: memanggil ulang saat konfirmasi tampil tidak membuat order baru
        $test->call('processDirectCheckout');
        $this->assertSame(1, Order::where('customer_name', 'Siti Rahma')->count());
    }

    public function test_direct_checkout_accepts_formatted_whatsapp_number_with_hyphens_and_spaces(): void
    {
        $material = Material::where('name', 'Tepung Tapioka')->firstOrFail();
        $pickup = now()->addDay()->setTime(8, 0)->format('Y-m-d\TH:i');

        Livewire::test(MaterialCatalog::class)
            ->call('incrementItem', $material->id)
            ->set('customerName', 'Bu Rina Format')
            ->set('customerPhone', '+62 812-3456-7890')
            ->set('pickupTime', $pickup)
            ->call('processDirectCheckout')
            ->assertHasNoErrors()
            ->assertSet('customerPhone', '+6281234567890')
            ->assertSet('showDirectCheckoutModal', true);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Bu Rina Format',
            'customer_phone' => '+6281234567890',
        ]);
    }

    public function test_direct_checkout_rejects_unavailable_materials(): void
    {
        $material = Material::where('name', 'Tepung Tapioka')->first();
        $material->update(['is_available' => false]);
        $pickup = now()->addDay()->setTime(8, 0)->format('Y-m-d\TH:i');

        Livewire::test(MaterialCatalog::class)
            ->set('selectedItems', [$material->id => 2])
            ->set('customerName', 'Pembeli Test')
            ->set('customerPhone', '08123456789')
            ->set('pickupTime', $pickup)
            ->call('processDirectCheckout')
            ->assertHasErrors(['cart']);

        $this->assertDatabaseMissing('orders', ['customer_name' => 'Pembeli Test']);
    }

    public function test_catalog_orders_categories_logically_and_groups_pelengkap_together(): void
    {
        Livewire::test(MaterialCatalog::class)
            ->assertSeeInOrder([
                'Kategori: Daging',
                'Kategori: Tepung',
                'Kategori: Bumbu',
                'Kategori: Pelengkap & Tambahan',
            ])
            ->assertSee('Telur Ayam')
            ->assertSee('Pengenyal Sobaso')
            ->assertSee('Penyedap Masako');
    }
}
