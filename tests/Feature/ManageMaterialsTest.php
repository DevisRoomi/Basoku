<?php

namespace Tests\Feature;

use App\Livewire\Admin\ManageMaterials;
use App\Livewire\MaterialCatalog;
use App\Livewire\RecipeCalculator;
use App\Models\Material;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\MaterialSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class ManageMaterialsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([AdminUserSeeder::class, MaterialSeeder::class]);
        $this->admin = User::where('email', 'admin@basoku.com')->first();
    }

    public function test_guest_cannot_access_manage_materials(): void
    {
        $response = $this->get('/admin/bahan');
        $response->assertRedirect('/masuk');
    }

    public function test_customer_cannot_access_manage_materials(): void
    {
        $customer = User::create([
            'name' => 'Pelanggan Biasa',
            'email' => 'customer@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $response = $this->actingAs($customer)->get('/admin/bahan');
        $response->assertRedirect('/masuk');
    }

    public function test_admin_can_view_materials_table(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/bahan');

        $response->assertStatus(200);
        $response->assertSeeLivewire(ManageMaterials::class);
        $response->assertSee('Kelola Harga Bahan');
        $response->assertSee('Daging Sapi');
        $response->assertSee('Tepung Tapioka');
    }

    public function test_admin_can_open_add_modal(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ManageMaterials::class)
            ->assertSet('showModal', false)
            ->call('openAddModal')
            ->assertSet('showModal', true)
            ->assertSet('isEditing', false)
            ->assertSet('name', '');
    }

    public function test_admin_can_add_new_material(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ManageMaterials::class)
            ->call('openAddModal')
            ->set('name', 'Lada Putih Bubuk')
            ->set('category', 'bumbu')
            ->set('unit', 'gram')
            ->set('package_unit', 'Pack')
            ->set('package_weight', 100.00)
            ->set('package_price', 6500.00)
            ->set('price', 65.00)
            ->set('is_available', true)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false)
            ->assertSee('Lada Putih Bubuk')
            ->assertSee('berhasil ditambahkan');

        $this->assertDatabaseHas('materials', [
            'name' => 'Lada Putih Bubuk',
            'category' => 'bumbu',
            'unit' => 'gram',
            'package_unit' => 'Pack',
            'package_weight' => 100.00,
            'package_price' => 6500.00,
            'price' => 65.00,
            'is_available' => true,
        ]);
    }

    public function test_admin_can_edit_existing_material(): void
    {
        $material = Material::where('name', 'Daging Sapi')->first();

        Livewire::actingAs($this->admin)
            ->test(ManageMaterials::class)
            ->call('openEditModal', $material->id)
            ->assertSet('showModal', true)
            ->assertSet('isEditing', true)
            ->assertSet('name', 'Daging Sapi')
            ->set('price', 145.00) // update price from 140 to 145
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false)
            ->assertSee('berhasil diperbarui');

        $this->assertDatabaseHas('materials', [
            'id' => $material->id,
            'price' => 145.00,
        ]);
    }

    public function test_validation_fails_when_fields_are_invalid(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ManageMaterials::class)
            ->call('openAddModal')
            ->set('name', '')
            ->set('price', -10)
            ->call('save')
            ->assertHasErrors(['name', 'price']);
    }

    public function test_admin_can_toggle_material_availability(): void
    {
        $material = Material::where('name', 'Daging Sapi')->first();
        $this->assertTrue((bool) $material->is_available);

        Livewire::actingAs($this->admin)
            ->test(ManageMaterials::class)
            ->call('toggleAvailability', $material->id)
            ->assertSee('diubah menjadi');

        $this->assertFalse((bool) $material->fresh()->is_available);

        // Toggle back to available
        Livewire::actingAs($this->admin)
            ->test(ManageMaterials::class)
            ->call('toggleAvailability', $material->id)
            ->assertSee('diubah menjadi');

        $this->assertTrue((bool) $material->fresh()->is_available);
    }

    public function test_price_change_instantly_affects_public_catalog_and_calculator(): void
    {
        $material = Material::where('name', 'Daging Sapi')->first();

        // 1. Admin updates price of Daging Sapi from 140 to 180
        Livewire::actingAs($this->admin)
            ->test(ManageMaterials::class)
            ->call('openEditModal', $material->id)
            ->set('price', 180.00)
            ->call('save')
            ->assertHasNoErrors();

        // 2. Verify that public MaterialCatalog immediately reflects the updated price
        Livewire::test(MaterialCatalog::class)
            ->assertSee('180');

        // 3. Verify that public RecipeCalculator uses the new price dynamically in UI and calculation
        Livewire::test(RecipeCalculator::class)
            ->set('startType', 'start_from_zero')
            ->set('meatType', 'sapi')
            ->set('meatWeight', 500)
            ->assertSee('Rp 180.000/kg')
            ->assertSet('totalProportionalCost', 99960.0);
    }

    public function test_admin_materials_displays_accurate_units_and_updates_selling_mode(): void
    {
        $garlic = Material::where('name', 'Bawang Putih Kupas')->first();

        Livewire::actingAs($this->admin)
            ->test(ManageMaterials::class)
            ->assertSee('Timbang Bebas')
            ->assertSee('Kemasan Utuh')
            ->assertSee('Eceran Satuan')
            ->assertDontSee('Rp 2.500 / g')
            ->assertDontSee('Rp 4.500 / g')
            ->call('openEditModal', $garlic->id)
            ->assertSet('selling_mode', 'curah')
            ->set('selling_mode', 'kemasan')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('materials', [
            'id' => $garlic->id,
            'selling_mode' => 'kemasan',
        ]);
    }

    public function test_updating_package_price_and_weight_auto_calculates_base_price_and_normalizes_commas(): void
    {
        $salt = Material::where('name', 'Garam Kasar')->first();

        // 1. Mode curah / kemasan: Rp 3.000 / 250g => Rp 12/g otomatis
        Livewire::actingAs($this->admin)
            ->test(ManageMaterials::class)
            ->call('openEditModal', $salt->id)
            ->set('package_weight', '250')
            ->set('package_price', '3000')
            ->assertSet('price', 12.0)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('materials', [
            'id' => $salt->id,
            'package_price' => 3000.00,
            'price' => 12.00,
        ]);

        // 2. Mode satuan: harga dasar otomatis sama dengan harga kemasan penuh (mendukung koma desimal)
        $egg = Material::where('name', 'Telur Ayam')->first();
        Livewire::actingAs($this->admin)
            ->test(ManageMaterials::class)
            ->call('openEditModal', $egg->id)
            ->set('selling_mode', 'satuan')
            ->set('package_price', '2750,5')
            ->assertSet('price', 2750.5)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('materials', [
            'id' => $egg->id,
            'package_price' => 2750.50,
            'price' => 2750.50,
        ]);
    }

    public function test_indonesian_thousands_separator_and_direct_price_edit_syncs_package_price(): void
    {
        $tapioka = Material::where('name', 'Tepung Tapioka')->firstOrFail();

        // 1. Input ribuan Indonesia "10.000" untuk 500g => 10000.0 & Rp 20/g (bukan Rp 10!)
        Livewire::actingAs($this->admin)
            ->test(ManageMaterials::class)
            ->call('openEditModal', $tapioka->id)
            ->set('package_price', '10.000')
            ->assertSet('price', 20.0)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('materials', [
            'id' => $tapioka->id,
            'package_price' => 10000.00,
            'price' => 20.00,
        ]);

        // 2. Edit langsung harga dasar daging (Rp 150/g) otomatis menyinkronkan package_price (Rp 150.000/kg)
        $beef = Material::where('name', 'Daging Sapi')->firstOrFail();
        Livewire::actingAs($this->admin)
            ->test(ManageMaterials::class)
            ->call('openEditModal', $beef->id)
            ->set('price', 150)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('materials', [
            'id' => $beef->id,
            'price' => 150.00,
            'package_price' => 150000.00,
        ]);
    }

    public function test_admin_cannot_create_duplicate_material_name_and_renaming_core_material_preserves_calculator_lookup(): void
    {
        // 1. Validasi nama unik menolak duplikasi
        Livewire::actingAs($this->admin)
            ->test(ManageMaterials::class)
            ->call('openAddModal')
            ->set('name', 'Daging Sapi')
            ->set('category', 'daging')
            ->set('price', 140)
            ->call('save')
            ->assertHasErrors(['name']);

        // 2. Rename bahan inti "Bawang Putih Kupas" -> "Bawang Lokal Super" + ubah harga ke Rp 60/g tetap terbaca via slug kanonik
        $garlic = Material::where('slug', 'bawang-putih-kupas')->firstOrFail();

        Livewire::actingAs($this->admin)
            ->test(ManageMaterials::class)
            ->call('openEditModal', $garlic->id)
            ->set('name', 'Bawang Lokal Super')
            ->set('package_weight', 100)
            ->set('package_price', 6000)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('bawang-putih-kupas', $garlic->fresh()->slug);

        $calc = Livewire::test(RecipeCalculator::class)
            ->set('startType', 'start_from_zero')
            ->set('meatWeight', 500);

        $garlicRow = collect($calc->get('breakdown'))->firstWhere('material_id', $garlic->id);
        $this->assertNotNull($garlicRow);
        $this->assertSame('Bawang Lokal Super', $garlicRow['name']);
        $this->assertEquals(60.0, (float) $garlicRow['price_per_gram']);
    }

    public function test_admin_can_delete_non_core_material_and_syncs_unit_for_satuan_items_while_protecting_core_materials(): void
    {
        // 1. Tambah bahan satuan baru dengan package_unit 'Ikat' tanpa mengisi unit -> unit otomatis sinkron menjadi 'Ikat'
        Livewire::actingAs($this->admin)
            ->test(ManageMaterials::class)
            ->call('openAddModal')
            ->set('name', 'Daun Seledri Segar')
            ->set('category', 'bumbu')
            ->set('selling_mode', 'satuan')
            ->set('package_unit', 'Ikat')
            ->set('package_weight', 50)
            ->set('package_price', 3000)
            ->call('save')
            ->assertHasNoErrors();

        $custom = Material::where('name', 'Daun Seledri Segar')->firstOrFail();
        $this->assertSame('Ikat', $custom->unit);

        // 2. Bahan non-inti bisa dihapus permanen lewat deleteMaterial
        Livewire::actingAs($this->admin)
            ->test(ManageMaterials::class)
            ->assertSee('Hapus')
            ->call('deleteMaterial', $custom->id)
            ->assertSee('telah dihapus');

        $this->assertDatabaseMissing('materials', ['id' => $custom->id]);

        // 3. Bahan inti kalkulator diproteksi dari hapus permanen
        $beef = Material::where('slug', 'daging-sapi')->firstOrFail();
        Livewire::actingAs($this->admin)
            ->test(ManageMaterials::class)
            ->call('deleteMaterial', $beef->id)
            ->assertSee('tidak dapat dihapus permanen');

        $this->assertDatabaseHas('materials', ['id' => $beef->id]);
    }

    public function test_material_cache_populates_and_auto_invalidates_on_admin_updates(): void
    {
        $cached = Material::cachedAll();
        $this->assertTrue(Cache::has(Material::CACHE_KEY));
        $beef = $cached->firstWhere('slug', 'daging-sapi');
        $this->assertNotNull($beef);

        Livewire::actingAs($this->admin)
            ->test(ManageMaterials::class)
            ->call('openEditModal', $beef->id)
            ->set('price', 155)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertFalse(Cache::has(Material::CACHE_KEY));
        $refreshed = Material::cachedAll()->firstWhere('slug', 'daging-sapi');
        $this->assertEquals(155.0, (float) $refreshed->price);
    }
}
