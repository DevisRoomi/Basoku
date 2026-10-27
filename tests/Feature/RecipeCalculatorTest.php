<?php

namespace Tests\Feature;

use App\Livewire\RecipeCalculator;
use App\Models\Order;
use Database\Seeders\MaterialSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RecipeCalculatorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(MaterialSeeder::class);
    }

    public function test_calculator_renders_initial_state_with_simulation_formula(): void
    {
        Livewire::test(RecipeCalculator::class)
            ->assertSee('Hitung Berdasarkan Berat')
            ->assertSee('Sesuaikan Anggaran')
            ->assertSee('Mulai dari Nol')
            ->assertSee('Sapi')
            ->assertSee('500 g')
            ->assertSee('Seimbang')
            ->assertSee('Pakai Jasa')
            ->assertSee('SIMULASI: Formula resep dan takaran kemasan sedang menunggu konfirmasi toko.')
            ->assertSee('Daging Sapi')
            ->assertSee('Tepung Tapioka')
            ->assertSee('Bawang Putih Kupas')
            ->assertSee('Garam Kasar')
            ->assertSee('Jasa Selepan Pasar')
            ->assertSee('Biaya Bahan Terpakai')
            ->assertSee('Uang Belanja Pasar');
    }

    public function test_scaling_meat_weight_updates_breakdown_and_totals(): void
    {
        $component500 = Livewire::test(RecipeCalculator::class)
            ->assertSet('totalProportionalCost', 79930.0);

        $component500->set('meatWeight', 1000)
            ->assertSet('totalProportionalCost', 159860.0);
    }

    public function test_own_meat_does_not_charge_for_meat(): void
    {
        Livewire::test(RecipeCalculator::class)
            ->set('startType', 'own_meat')
            ->assertSet('totalProportionalCost', 9930.0)
            ->assertSee('Bawa Sendiri');
    }

    public function test_toggle_grinding_adjusts_totals(): void
    {
        Livewire::test(RecipeCalculator::class)
            ->assertSet('useGrinding', true)
            ->assertSet('totalProportionalCost', 79930.0)
            ->toggle('useGrinding')
            ->assertSet('useGrinding', false)
            ->assertSet('totalProportionalCost', 72430.0);
    }

    public function test_budget_mode_displays_error_when_budget_is_insufficient(): void
    {
        Livewire::test(RecipeCalculator::class)
            ->set('calculationMode', 'budget')
            ->set('budget', 50000)
            ->assertSet('budgetError', 'Anggaran Anda belum mencukupi. Minimal butuh Rp 79.930 untuk racikan standar 500 gram.')
            ->assertSee('Anggaran Belum Mencukupi')
            ->assertSee('Minimal butuh Rp 79.930 untuk racikan standar 500 gram.')
            ->assertSet('breakdown', [])
            ->assertSet('totalProportionalCost', 0.0);
    }

    public function test_budget_mode_calculates_meat_weight_when_budget_is_sufficient(): void
    {
        Livewire::test(RecipeCalculator::class)
            ->set('calculationMode', 'budget')
            ->set('budget', 100000)
            ->assertSet('budgetError', null)
            ->assertSet('meatWeight', 625)
            ->assertDontSee('Anggaran Belum Mencukupi')
            ->assertSee('625 g');
    }

    public function test_budget_mode_with_own_meat_has_lower_minimum_threshold(): void
    {
        Livewire::test(RecipeCalculator::class)
            ->set('startType', 'own_meat')
            ->set('calculationMode', 'budget')
            ->set('budget', 50000)
            ->assertSet('budgetError', null)
            ->assertSet('meatWeight', 2517)
            ->assertDontSee('Anggaran Belum Mencukupi');
    }

    public function test_open_and_close_checkout_modal(): void
    {
        Livewire::test(RecipeCalculator::class)
            ->assertSet('showCheckoutModal', false)
            ->call('openCheckout')
            ->assertSet('showCheckoutModal', true)
            ->assertSee('Data Pengambilan')
            ->assertSee('Nama Lengkap')
            ->call('closeCheckout')
            ->assertSet('showCheckoutModal', false)
            ->assertDontSee('Data Pengambilan');
    }

    public function test_checkout_validation_requires_name_and_pickup_time(): void
    {
        Livewire::test(RecipeCalculator::class)
            ->call('openCheckout')
            ->set('customerName', '')
            ->set('pickupTime', '')
            ->call('processCheckout')
            ->assertHasErrors(['customerName', 'pickupTime']);
    }

    public function test_process_checkout_saves_order_and_items_and_redirects_to_whatsapp(): void
    {
        $pickupDate = now()->addDays(2)->setTime(8, 0)->format('Y-m-d\TH:i');

        $test = Livewire::test(RecipeCalculator::class)
            ->set('startType', 'start_from_zero')
            ->set('meatType', 'sapi')
            ->set('meatWeight', 500)
            ->set('preference', 'seimbang')
            ->set('useGrinding', true)
            ->call('openCheckout')
            ->set('customerName', 'Pak Sukadi')
            ->set('pickupTime', $pickupDate)
            ->call('processCheckout');

        // 1. Assert database record exists in orders table
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Pak Sukadi',
            'start_type' => 'start_from_zero',
            'meat_weight' => 500.000,
            'preference' => 'seimbang',
            'grinding_fee' => 7500.00,
            'total_price' => 89000.00, // Uang belanja pasar kemasan utuh (daging 500g + tapioka 250g pack + bumbu pasar + selepan)
            'status' => 'pending',
        ]);

        $order = Order::where('customer_name', 'Pak Sukadi')->first();
        $this->assertNotNull($order);

        // 2. Assert order items are saved in order_items table
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'material_name' => 'Daging Sapi',
            'quantity' => 500.000,
            'unit_price' => 140.00,
            'subtotal' => 70000.00,
        ]);

        // Tapioka: butuh 100g, beli kemasan utuh 250g @ Rp 18/g = Rp 4.500
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'material_name' => 'Tepung Tapioka',
            'quantity' => 100.000,
            'unit_price' => 18.00,
            'subtotal' => 4500.00,
        ]);

        // 3. Assert redirect to WhatsApp
        $test->assertRedirect();
        $redirectUrl = $test->effects['redirect'] ?? '';
        $this->assertStringContainsString('https://wa.me/6285122797969', $redirectUrl);
        $this->assertStringContainsString(urlencode('Pak Sukadi'), $redirectUrl);
    }

    public function test_flour_packaging_market_rules(): void
    {
        // 1. Daging 500g rasio seimbang (0.20) -> tepung 100g (di bawah 250g) -> wajib beli 1x 250g
        $component = Livewire::test(RecipeCalculator::class)
            ->set('meatWeight', 500);

        $breakdown = $component->get('breakdown');
        $tapioca = collect($breakdown)->firstWhere('name', 'Tepung Tapioka');
        $this->assertNotNull($tapioca);
        $this->assertEquals(100.0, $tapioca['weight']);
        $this->assertStringContainsString('1x 250g', $tapioca['note']);
        $this->assertEquals(4500.0, $tapioca['market_cost']); // 250g * Rp 18

        // 2. Daging 3750g rasio seimbang (0.20) -> tepung 750g -> wajib beli 1x 500g + 1x 250g
        $component->set('meatWeight', 3750);
        $breakdown = $component->get('breakdown');
        $tapioca = collect($breakdown)->firstWhere('name', 'Tepung Tapioka');
        $this->assertEquals(750.0, $tapioca['weight']);
        $this->assertStringContainsString('1x 500g + 1x 250g', $tapioca['note']);
        $this->assertEquals(13500.0, $tapioca['market_cost']); // 750g * Rp 18

        // 3. Daging 4000g rasio seimbang (0.20) -> tepung 800g (di atas 750g di bawah 1kg) -> wajib beli 1x 1 kg
        $component->set('meatWeight', 4000);
        $breakdown = $component->get('breakdown');
        $tapioca = collect($breakdown)->firstWhere('name', 'Tepung Tapioka');
        $this->assertEquals(800.0, $tapioca['weight']);
        $this->assertStringContainsString('1x 1 kg', $tapioca['note']);
        $this->assertEquals(18000.0, $tapioca['market_cost']); // 1000g * Rp 18
    }

    public function test_checkout_ignores_tampered_client_price_and_recalculates_server_side(): void
    {
        $pickupDate = now()->addDays(2)->setTime(8, 0)->format('Y-m-d\TH:i');

        Livewire::test(RecipeCalculator::class)
            ->set('meatWeight', 500)
            ->set('startType', 'start_from_zero')
            ->set('preference', 'seimbang')
            ->set('useGrinding', true)
            // Manipulasi nilai publik oleh attacker
            ->set('totalProportionalCost', 100.0)
            ->set('breakdown', [
                [
                    'material_id' => 1,
                    'name' => 'Daging Sapi',
                    'weight' => 500,
                    'cost' => 100.0,
                    'proportional_cost' => 100.0,
                    'market_cost' => 100.0,
                ],
            ])
            ->call('openCheckout')
            ->set('customerName', 'Attacker Tamper')
            ->set('pickupTime', $pickupDate)
            ->call('processCheckout');

        // Order yang tersimpan harus menggunakan kalkulasi belanja pasar server, bukan nilai Rp 100
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Attacker Tamper',
            'total_price' => 89000.00,
        ]);
    }

    public function test_pickup_time_validation_rejects_past_time_and_outside_operating_hours(): void
    {
        // 1. Kurang dari 1 jam dari sekarang
        $tooSoon = now()->addMinutes(20)->format('Y-m-d\TH:i');

        Livewire::test(RecipeCalculator::class)
            ->set('meatWeight', 500)
            ->call('openCheckout')
            ->set('customerName', 'Budi Santoso')
            ->set('pickupTime', $tooSoon)
            ->call('processCheckout')
            ->assertHasErrors(['pickupTime']);

        // 2. Di luar jam operasional (kios tutup: misal pkl 16.00 WIB)
        $outsideHours = now()->addDays(1)->setTime(16, 0)->format('Y-m-d\TH:i');

        Livewire::test(RecipeCalculator::class)
            ->set('meatWeight', 500)
            ->call('openCheckout')
            ->set('customerName', 'Budi Santoso')
            ->set('pickupTime', $outsideHours)
            ->call('processCheckout')
            ->assertHasErrors(['pickupTime']);

        // 3. Valid: jam buka kios (pkl 08.00 WIB besok)
        $validPickup = now()->addDays(1)->setTime(8, 0)->format('Y-m-d\TH:i');

        Livewire::test(RecipeCalculator::class)
            ->set('meatWeight', 500)
            ->call('openCheckout')
            ->set('customerName', 'Budi Santoso')
            ->set('pickupTime', $validPickup)
            ->call('processCheckout')
            ->assertHasNoErrors();
    }

    public function test_calculator_loads_saved_recipe_from_query_parameter(): void
    {
        $user = \App\Models\User::factory()->create();
        $recipe = \App\Models\SavedRecipe::create([
            'user_id' => $user->id,
            'name' => 'Resep Bakso Spesial Urat',
            'meat_weight' => 1250,
            'start_type' => 'start_from_zero',
            'preference' => 'urat',
            'recipe_details' => [],
            'estimated_total' => 150000,
        ]);

        Livewire::withQueryParams(['recipe_id' => $recipe->id])
            ->test(RecipeCalculator::class)
            ->assertSet('meatWeight', 1250)
            ->assertSet('preference', 'urat')
            ->assertSet('startType', 'start_from_zero');
    }
}
