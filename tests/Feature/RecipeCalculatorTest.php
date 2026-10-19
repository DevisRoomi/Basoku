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
        $pickupDate = now()->addDays(2)->format('Y-m-d\TH:i');

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
            'total_price' => 79930.00,
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

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'material_name' => 'Tepung Tapioka',
            'quantity' => 100.000,
            'unit_price' => 18.00,
            'subtotal' => 1800.00,
        ]);

        // 3. Assert redirect to WhatsApp
        $test->assertRedirect();
        $redirectUrl = $test->effects['redirect'] ?? '';
        $this->assertStringContainsString('https://wa.me/6285122797969', $redirectUrl);
        $this->assertStringContainsString(urlencode('Pak Sukadi'), $redirectUrl);
    }
}
