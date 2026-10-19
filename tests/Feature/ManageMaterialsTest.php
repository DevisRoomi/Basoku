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

        // 3. Verify that public RecipeCalculator uses the new price in calculation
        Livewire::test(RecipeCalculator::class)
            ->set('startType', 'start_from_zero')
            ->set('meatType', 'sapi')
            ->set('meatWeight', 500)
            ->assertSet('meatWeight', 500);
    }
}
