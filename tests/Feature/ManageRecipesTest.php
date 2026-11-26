<?php

namespace Tests\Feature;

use App\Livewire\Admin\ManageRecipes;
use App\Livewire\RecipeCalculator;
use App\Models\RecipeFormula;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\MaterialSeeder;
use Database\Seeders\RecipeFormulaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageRecipesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminUserSeeder::class);
        $this->seed(RecipeFormulaSeeder::class);
        $this->seed(MaterialSeeder::class);
    }

    protected function getAdminUser(): User
    {
        return User::where('email', 'admin@basoku.com')->first();
    }

    protected function getCustomerUser(): User
    {
        return User::create([
            'name' => 'Pelanggan Biasa',
            'email' => 'customer@example.com',
            'phone' => '081234567890',
            'role' => 'customer',
            'password' => bcrypt('password123'),
        ]);
    }

    public function test_guest_and_non_admin_cannot_access_manage_recipes_page(): void
    {
        // 1. Tamu tak terautentikasi dialihkan ke /masuk
        $this->get('/admin/resep')->assertRedirect('/masuk');
        $this->get('/admin/recipes')->assertRedirect('/masuk');

        // 2. Pelanggan biasa ditolak / dialihkan
        $customer = $this->getCustomerUser();
        $this->actingAs($customer)->get('/admin/resep')->assertRedirect('/masuk');
    }

    public function test_admin_can_view_manage_recipes_page(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get('/admin/resep');
        $response->assertStatus(200);
        $response->assertSee('Konfigurasi Formula Resep');
        $response->assertSee('Racikan Seimbang');
        $response->assertSee('Racikan Enak (Spesial Daging)');
        $response->assertSee('Racikan Ekonomis');

        // Alias /admin/recipes
        $aliasResponse = $this->actingAs($admin)->get('/admin/recipes');
        $aliasResponse->assertRedirect('/admin/resep');
    }

    public function test_admin_can_update_recipe_formula_ratios(): void
    {
        $admin = $this->getAdminUser();
        $formula = RecipeFormula::where('code', 'seimbang')->first();

        Livewire::actingAs($admin)
            ->test(ManageRecipes::class)
            ->call('openEditModal', $formula->id)
            ->assertSet('showModal', true)
            ->assertSet('code', 'seimbang')
            ->set('flour_ratio', 0.2500)
            ->set('spice_ratio', 0.0600)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false)
            ->assertSee('berhasil diperbarui');

        $this->assertDatabaseHas('recipe_formulas', [
            'code' => 'seimbang',
            'flour_ratio' => 0.2500,
            'spice_ratio' => 0.0600,
        ]);
    }

    public function test_admin_can_toggle_simulation_status(): void
    {
        $admin = $this->getAdminUser();
        $formula = RecipeFormula::where('code', 'seimbang')->first();
        $this->assertFalse($formula->is_simulation);

        Livewire::actingAs($admin)
            ->test(ManageRecipes::class)
            ->call('toggleSimulation', $formula->id)
            ->assertSee('diubah menjadi: Simulasi');

        $this->assertDatabaseHas('recipe_formulas', [
            'code' => 'seimbang',
            'is_simulation' => true,
        ]);

        // Toggle kembali ke resmi
        Livewire::actingAs($admin)
            ->test(ManageRecipes::class)
            ->call('toggleSimulation', $formula->id)
            ->assertSee('diubah menjadi: Resmi');

        $this->assertDatabaseHas('recipe_formulas', [
            'code' => 'seimbang',
            'is_simulation' => false,
        ]);
    }

    public function test_calculator_responds_dynamically_to_formula_ratio_changes(): void
    {
        // Kondisi awal (seimbang resmi 1:1):
        // 500g daging sapi (140/g = 70.000)
        // Tepung 500g (flour_ratio 1.0, 18/g = 9.000)
        // Bawang 24g (40/g = 960), Garam 16g (6/g = 96)
        // Selepan 7.500 -> Total: 87.556.0
        Livewire::test(RecipeCalculator::class)
            ->set('preference', 'seimbang')
            ->assertSet('totalProportionalCost', 87556.0);

        // Ubah flour_ratio dari 1.0000 (500g) menjadi 0.3000 (150g -> 150*18 = 2.700, selisih -6.300)
        $formula = RecipeFormula::where('code', 'seimbang')->first();
        $formula->update([
            'flour_ratio' => 0.3000,
        ]);

        // Kalkulator langsung merespons perubahan dari basis data
        Livewire::test(RecipeCalculator::class)
            ->set('preference', 'seimbang')
            ->assertSet('totalProportionalCost', 81256.0);
    }

    public function test_calculator_hides_simulation_warning_when_formula_is_not_simulation(): void
    {
        $formula = RecipeFormula::where('code', 'seimbang')->first();

        // 1. Saat is_simulation = true, tampilkan peringatan simulasi
        $formula->update([
            'is_simulation' => true,
        ]);

        Livewire::test(RecipeCalculator::class)
            ->set('preference', 'seimbang')
            ->assertSee('SIMULASI: Formula resep dan takaran kemasan sedang menunggu konfirmasi toko.');

        // 2. Ubah is_simulation menjadi false
        $formula->update([
            'is_simulation' => false,
        ]);

        // 3. Label simulasi hilang, digantikan oleh lencana resmi terverifikasi
        Livewire::test(RecipeCalculator::class)
            ->set('preference', 'seimbang')
            ->assertDontSee('SIMULASI: Formula resep dan takaran kemasan sedang menunggu konfirmasi toko.')
            ->assertSee('RESMI: Formula resep terverifikasi langsung oleh kios selepan BASOKU.');
    }

    public function test_admin_can_reset_formulas_to_defaults(): void
    {
        $admin = $this->getAdminUser();

        // Modifikasi data formula
        RecipeFormula::where('code', 'seimbang')->update([
            'flour_ratio' => 0.4500,
            'is_simulation' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(ManageRecipes::class)
            ->call('resetToDefaults')
            ->assertSee('berhasil dipulihkan');

        $this->assertDatabaseHas('recipe_formulas', [
            'code' => 'seimbang',
            'flour_ratio' => 1.0000,
            'spice_ratio' => 0.0800,
            'is_simulation' => false,
        ]);
    }

    public function test_calculator_normalizes_ratios_when_meat_ratio_differs_from_one(): void
    {
        RecipeFormula::where('code', 'enak')->update([
            'meat_ratio' => 2.0000,
            'flour_ratio' => 0.4000, // 0.4 / 2.0 = 20% tepung
            'spice_ratio' => 0.1000, // 0.1 / 2.0 = 5% bumbu
        ]);

        Livewire::test(RecipeCalculator::class)
            ->set('meatWeight', 500)
            ->set('preference', 'enak')
            ->assertSet('totalProportionalCost', 79960.0);
    }

    public function test_admin_can_update_ratios_using_indonesian_comma_decimals_and_no_tilde_rendered(): void
    {
        $admin = $this->getAdminUser();
        $formula = RecipeFormula::where('code', 'enak')->first();

        Livewire::actingAs($admin)
            ->test(ManageRecipes::class)
            ->assertDontSee('~', false)
            ->call('openEditModal', $formula->id)
            ->set('meat_ratio', '1,0')
            ->set('flour_ratio', '0,2')
            ->set('spice_ratio', '0,08')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('recipe_formulas', [
            'code' => 'enak',
            'meat_ratio' => 1.0000,
            'flour_ratio' => 0.2000,
            'spice_ratio' => 0.0800,
        ]);
    }
}
