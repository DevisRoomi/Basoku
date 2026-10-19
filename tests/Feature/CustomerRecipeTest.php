<?php

namespace Tests\Feature;

use App\Livewire\Auth\Register;
use App\Livewire\Customer\MyRecipeLab;
use App\Livewire\RecipeCalculator;
use App\Models\SavedRecipe;
use App\Models\User;
use Database\Seeders\MaterialSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerRecipeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(MaterialSeeder::class);
    }

    public function test_guest_can_render_register_page(): void
    {
        $response = $this->get('/daftar');

        $response->assertStatus(200);
        $response->assertSeeLivewire(Register::class);
        $response->assertSee('Daftar Akun Baru');
    }

    public function test_customer_can_register_and_redirects_to_my_recipe_lab(): void
    {
        Livewire::test(Register::class)
            ->set('name', 'Budi Santoso')
            ->set('email', 'budi@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect('/racikan-saya');

        $this->assertDatabaseHas('users', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'role' => 'customer',
        ]);

        $this->assertTrue(Auth::check());
        $this->assertEquals('customer', Auth::user()->role);
    }

    public function test_registration_validation_fails_on_duplicate_email(): void
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        Livewire::test(Register::class)
            ->set('name', 'New Name')
            ->set('email', 'existing@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['email']);
    }

    public function test_guest_saving_recipe_redirects_to_login(): void
    {
        Livewire::test(RecipeCalculator::class)
            ->call('openSaveModal')
            ->assertRedirect('/masuk');
    }

    public function test_authenticated_customer_can_save_recipe(): void
    {
        $customer = User::create([
            'name' => 'Bu Dewi',
            'email' => 'dewi@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        Livewire::actingAs($customer)
            ->test(RecipeCalculator::class)
            ->set('startType', 'start_from_zero')
            ->set('meatType', 'sapi')
            ->set('meatWeight', 500)
            ->set('preference', 'enak')
            ->call('openSaveModal')
            ->assertSet('showSaveModal', true)
            ->set('recipeName', 'Bakso Urat Istimewa Idul Fitri')
            ->call('saveRecipe')
            ->assertHasNoErrors()
            ->assertSet('showSaveModal', false)
            ->assertSee('berhasil disimpan');

        $this->assertDatabaseHas('saved_recipes', [
            'user_id' => $customer->id,
            'name' => 'Bakso Urat Istimewa Idul Fitri',
            'meat_weight' => 500,
            'preference' => 'enak',
            'start_type' => 'start_from_zero',
        ]);

        $savedRecipe = SavedRecipe::where('name', 'Bakso Urat Istimewa Idul Fitri')->first();
        $this->assertIsArray($savedRecipe->recipe_details);
        $this->assertNotEmpty($savedRecipe->recipe_details);
    }

    public function test_customer_can_view_saved_recipes_in_my_recipe_lab(): void
    {
        $customer = User::create([
            'name' => 'Mas Danu',
            'email' => 'danu@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        SavedRecipe::create([
            'user_id' => $customer->id,
            'name' => 'Racikan Rahasia Danu',
            'meat_weight' => 750,
            'start_type' => 'start_from_zero',
            'preference' => 'seimbang',
            'recipe_details' => [
                ['name' => 'Daging Sapi', 'weight' => 750, 'unit' => 'gram', 'cost' => 105000],
                ['name' => 'Tepung Tapioka', 'weight' => 120, 'unit' => 'gram', 'cost' => 2160],
            ],
            'estimated_total' => 107160,
        ]);

        $response = $this->actingAs($customer)->get('/racikan-saya');

        $response->assertStatus(200);
        $response->assertSeeLivewire(MyRecipeLab::class);
        $response->assertSee('Racikan Rahasia Danu');
        $response->assertSee('750g Daging');
    }

    public function test_customer_cannot_see_other_users_saved_recipes(): void
    {
        $userA = User::create([
            'name' => 'User A',
            'email' => 'usera@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $userB = User::create([
            'name' => 'User B',
            'email' => 'userb@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        SavedRecipe::create([
            'user_id' => $userA->id,
            'name' => 'Resep Rahasia User A',
            'meat_weight' => 500,
            'start_type' => 'start_from_zero',
            'preference' => 'enak',
            'recipe_details' => [],
            'estimated_total' => 80000,
        ]);

        Livewire::actingAs($userB)
            ->test(MyRecipeLab::class)
            ->assertDontSee('Resep Rahasia User A')
            ->assertSee('Belum Ada Racikan Tersimpan');
    }

    public function test_customer_can_delete_saved_recipe(): void
    {
        $customer = User::create([
            'name' => 'Customer Hapus',
            'email' => 'hapus@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $recipe = SavedRecipe::create([
            'user_id' => $customer->id,
            'name' => 'Resep Akan Dihapus',
            'meat_weight' => 500,
            'start_type' => 'start_from_zero',
            'preference' => 'ekonomis',
            'recipe_details' => [],
            'estimated_total' => 50000,
        ]);

        Livewire::actingAs($customer)
            ->test(MyRecipeLab::class)
            ->call('deleteRecipe', $recipe->id)
            ->assertSee('telah dihapus');

        $this->assertDatabaseMissing('saved_recipes', [
            'id' => $recipe->id,
        ]);
    }
}
