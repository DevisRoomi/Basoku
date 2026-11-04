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
            ->set('customerPhone', '')
            ->set('pickupTime', '')
            ->call('processCheckout')
            ->assertHasErrors(['customerName', 'customerPhone', 'pickupTime']);
    }

    public function test_process_checkout_saves_order_and_items_and_redirects_to_whatsapp(): void
    {
        $pickupDate = now()->addDays(2)->setTime(8, 0)->format('Y-m-d\TH:i');

        $test = Livewire::test(RecipeCalculator::class)
            ->set('startType', 'start_from_zero')
            ->set('meatType', 'sapi')
            ->set('meatWeight', 500)
            ->set('flourType', 'tapioka')
            ->set('preference', 'seimbang')
            ->set('useGrinding', true)
            ->call('openCheckout')
            ->set('customerName', 'Pak Sukadi')
            ->set('customerPhone', '081234567890')
            ->set('pickupTime', $pickupDate)
            ->call('processCheckout');

        // 1. Assert database record exists in orders table
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Pak Sukadi',
            'customer_phone' => '081234567890',
            'start_type' => 'start_from_zero',
            'meat_weight' => 500.000,
            'preference' => 'seimbang',
            'grinding_fee' => 7500.00,
            'total_price' => 93500.00, // Uang belanja pasar kemasan utuh (daging 500g + tapioka 500g pack + bumbu pasar + selepan)
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

        // Tapioka: butuh 100g, beli kemasan utuh pack 500g = Rp 9.000
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'material_name' => 'Tepung Tapioka',
            'quantity' => 100.000,
            'unit_price' => 18.00,
            'subtotal' => 9000.00,
        ]);

        // 3. Assert redirect to WhatsApp
        $test->assertRedirect();
        $redirectUrl = $test->effects['redirect'] ?? '';
        $this->assertStringContainsString('https://wa.me/6285122797969', $redirectUrl);
        $this->assertStringContainsString(urlencode('Pak Sukadi'), $redirectUrl);
    }

    public function test_flour_packaging_market_rules(): void
    {
        // 1. Daging 500g rasio seimbang (0.20) -> tepung tapioka 100g (kemasan pack 500g) -> wajib beli 1 pack @500g
        $component = Livewire::test(RecipeCalculator::class)
            ->set('meatWeight', 500);

        $breakdown = $component->get('breakdown');
        $tapioca = collect($breakdown)->firstWhere('name', 'Tepung Tapioka');
        $this->assertNotNull($tapioca);
        $this->assertEquals(100.0, $tapioca['weight']);
        $this->assertStringContainsString('Beli 1 Pack @500g', $tapioca['note']);
        $this->assertEquals(9000.0, $tapioca['market_cost']); // 1 pack * Rp 9.000

        // 2. Daging 3750g rasio seimbang (0.20) -> tepung tapioka 750g -> wajib beli 2 pack @500g = 1.000g
        $component->set('meatWeight', 3750);
        $breakdown = $component->get('breakdown');
        $tapioca = collect($breakdown)->firstWhere('name', 'Tepung Tapioka');
        $this->assertEquals(750.0, $tapioca['weight']);
        $this->assertStringContainsString('Beli 2 Pack @500g', $tapioca['note']);
        $this->assertEquals(18000.0, $tapioca['market_cost']); // 2 pack * Rp 9.000

        // 3. Varian ProBakso (kemasan pack 250g, Rp 10.000/pack) untuk 500g daging -> butuh 100g -> wajib beli 1 pack
        $component->set('meatWeight', 500)
            ->set('flourType', 'probakso');
        $breakdown = $component->get('breakdown');
        $probakso = collect($breakdown)->firstWhere('name', 'Tepung ProBakso');
        $this->assertNotNull($probakso);
        $this->assertEquals(100.0, $probakso['weight']);
        $this->assertStringContainsString('Beli 1 Pack @250g', $probakso['note']);
        $this->assertEquals(10000.0, $probakso['market_cost']); // 1 pack * Rp 10.000
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
            ->set('customerPhone', '081234567890')
            ->set('pickupTime', $pickupDate)
            ->call('processCheckout');

        // Order yang tersimpan harus menggunakan kalkulasi belanja pasar server, bukan nilai Rp 100
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Attacker Tamper',
            'customer_phone' => '081234567890',
            'total_price' => 93500.00,
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
            ->set('customerPhone', '081234567890')
            ->set('pickupTime', $tooSoon)
            ->call('processCheckout')
            ->assertHasErrors(['pickupTime']);

        // 2. Di luar jam operasional (kios tutup: misal pkl 16.00 WIB)
        $outsideHours = now()->addDays(1)->setTime(16, 0)->format('Y-m-d\TH:i');

        Livewire::test(RecipeCalculator::class)
            ->set('meatWeight', 500)
            ->call('openCheckout')
            ->set('customerName', 'Budi Santoso')
            ->set('customerPhone', '081234567890')
            ->set('pickupTime', $outsideHours)
            ->call('processCheckout')
            ->assertHasErrors(['pickupTime']);

        // 3. Valid: jam buka kios (pkl 08.00 WIB besok)
        $validPickup = now()->addDays(1)->setTime(8, 0)->format('Y-m-d\TH:i');

        Livewire::test(RecipeCalculator::class)
            ->set('meatWeight', 500)
            ->call('openCheckout')
            ->set('customerName', 'Budi Santoso')
            ->set('customerPhone', '081234567890')
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

    public function test_custom_flour_ratio_calculation(): void
    {
        // 1000g daging, rasio kustom 25% -> tepung 250g
        Livewire::test(RecipeCalculator::class)
            ->set('meatWeight', 1000)
            ->set('preference', 'kustom')
            ->set('customFlourRatio', 25)
            ->assertSee('Kustom Bebas')
            ->assertSee('25% Tepung');
    }

    public function test_ingredients_only_mode_without_meat(): void
    {
        Livewire::test(RecipeCalculator::class)
            ->set('startType', 'ingredients_only')
            ->set('flourType', 'tapioka')
            ->set('ingredientFlourPacks', 2)
            ->set('ingredientGarlicPacks', 1)
            ->assertSet('meatWeight', 0)
            ->assertSet('useGrinding', false)
            ->assertSee('Beli Bahan Saja')
            ->assertSee('Tanpa Daging');
    }
}
