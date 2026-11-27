<?php

namespace Tests\Feature;

use App\Livewire\RecipeCalculator;
use App\Models\Material;
use App\Models\Order;
use App\Models\RecipeFormula;
use App\Models\SavedRecipe;
use App\Models\User;
use Database\Seeders\MaterialSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
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
            ->assertSet('preference', 'kustom')
            ->assertSet('customFlourRatio', 20)
            ->assertSee('Hitung Berdasarkan Berat')
            ->assertSee('Sesuaikan Anggaran')
            ->assertSee('Mulai dari Nol')
            ->assertSee('Sapi')
            ->assertSee('500 g')
            ->assertSee('Kustom Bebas')
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
            ->assertSet('totalProportionalCost', 79960.0);

        $component500->set('meatWeight', 1000)
            ->assertSet('totalProportionalCost', 159920.0);
    }

    public function test_own_meat_does_not_charge_for_meat(): void
    {
        Livewire::test(RecipeCalculator::class)
            ->set('startType', 'own_meat')
            ->assertSet('totalProportionalCost', 9960.0)
            ->assertSee('Bawa Sendiri');
    }

    public function test_toggle_grinding_adjusts_totals(): void
    {
        Livewire::test(RecipeCalculator::class)
            ->assertSet('useGrinding', true)
            ->assertSet('totalProportionalCost', 79960.0)
            ->toggle('useGrinding')
            ->assertSet('useGrinding', false)
            ->assertSet('totalProportionalCost', 72460.0);
    }

    public function test_budget_mode_displays_error_when_budget_is_insufficient(): void
    {
        Livewire::test(RecipeCalculator::class)
            ->set('calculationMode', 'budget')
            ->set('budget', 50000)
            ->assertSet('budgetError', 'Anggaran Anda belum mencukupi. Minimal butuh Rp 79.960 untuk racikan standar 500 gram.')
            ->assertSee('Anggaran Belum Mencukupi')
            ->assertSee('Minimal butuh Rp 79.960 untuk racikan standar 500 gram.')
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
            ->assertSet('meatWeight', 2510)
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
            'total_price' => 88000.00, // Daging 70rb + Tapioka 1 pack 9rb + Bawang curah 1rb + Garam curah 500 + Selepan 7,5rb
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

        // Tapioka (seimbang resmi 1:1): butuh 500g, beli kemasan utuh 1 pack @500g = Rp 9.000
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'material_name' => 'Tepung Tapioka',
            'quantity' => 500.000,
            'unit_price' => 18.00,
            'subtotal' => 9000.00,
        ]);

        // 3. Assert confirmation modal stays open with #BSK- order code, single Jasa Selepan WA URL, and track URL
        $expectedOrderCode = '#BSK-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
        $test->assertSet('showCheckoutModal', true)
            ->assertSet('confirmedOrderCode', $expectedOrderCode)
            ->assertSee('Pesanan Berhasil Dicatat')
            ->assertSee($expectedOrderCode);

        $waUrl = (string) $test->get('confirmedWaUrl');
        $decodedWa = urldecode($waUrl);
        $this->assertStringContainsString('https://wa.me/6285122797969', $waUrl);
        $this->assertStringContainsString(urlencode('Pak Sukadi'), $waUrl);
        $this->assertStringContainsString(urlencode($expectedOrderCode), $waUrl);
        $this->assertSame(1, substr_count($decodedWa, 'Jasa Selepan'));

        $trackUrl = (string) $test->get('confirmedTrackUrl');
        $this->assertStringContainsString('/lacak-pesanan', $trackUrl);
        $this->assertStringContainsString(urlencode($expectedOrderCode), $trackUrl);

        // 4. Calling processCheckout again while confirmation is active must NOT create a duplicate order
        $test->call('processCheckout');
        $this->assertSame(1, Order::where('customer_name', 'Pak Sukadi')->count());
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
        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::test(RecipeCalculator::class)
            ->set('meatWeight', 500)
            ->set('startType', 'start_from_zero')
            ->set('preference', 'seimbang')
            ->set('useGrinding', true)
            // Manipulasi nilai publik terkunci oleh attacker wajib ditolak langsung oleh Livewire
            ->set('totalProportionalCost', 100.0);
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
        $user = User::factory()->create();
        $extraMat = Material::where('name', 'Telur Ayam')->first();
        $recipe = SavedRecipe::create([
            'user_id' => $user->id,
            'name' => 'Resep Bakso Ayam ProBakso Kustom',
            'meat_weight' => 1250,
            'start_type' => 'start_from_zero',
            'preference' => 'kustom',
            'recipe_details' => [
                ['name' => 'Daging Ayam', 'weight' => 1250, 'is_meat' => true],
                ['name' => 'Tepung ProBakso', 'weight' => 375],
                ['name' => 'Telur Ayam', 'material_id' => $extraMat->id, 'qty' => 2, 'weight' => 120, 'is_extra' => true],
            ],
            'estimated_total' => 150000,
        ]);

        Livewire::actingAs($user)
            ->withQueryParams(['recipe_id' => $recipe->id])
            ->test(RecipeCalculator::class)
            ->assertSet('meatWeight', 1250)
            ->assertSet('meatType', 'ayam')
            ->assertSet('flourType', 'probakso')
            ->assertSet('preference', 'kustom')
            ->assertSet('customFlourRatio', 30)
            ->assertSet('useGrinding', false)
            ->assertSet('extraItems.'.$extraMat->id, 2)
            ->assertSet('startType', 'start_from_zero');
    }

    public function test_calculator_blocks_loading_other_users_saved_recipe_idor(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $recipe = SavedRecipe::create([
            'user_id' => $owner->id,
            'name' => 'Resep Rahasia Pemilik',
            'meat_weight' => 2750,
            'start_type' => 'own_meat',
            'preference' => 'enak',
            'recipe_details' => [],
            'estimated_total' => 150000,
        ]);

        // Guest tidak boleh memuat resep milik pengguna lain
        Livewire::withQueryParams(['recipe_id' => $recipe->id])
            ->test(RecipeCalculator::class)
            ->assertSet('meatWeight', 500)
            ->assertSet('preference', 'kustom')
            ->assertSet('startType', 'start_from_zero');

        // User lain tidak boleh memuat resep milik $owner
        Livewire::actingAs($otherUser)
            ->withQueryParams(['recipe_id' => $recipe->id])
            ->test(RecipeCalculator::class)
            ->assertSet('meatWeight', 500)
            ->assertSet('preference', 'kustom')
            ->assertSet('startType', 'start_from_zero');
    }

    public function test_switching_from_ingredients_only_back_to_meat_mode_restores_valid_meat_weight(): void
    {
        Livewire::test(RecipeCalculator::class)
            ->set('startType', 'ingredients_only')
            ->assertSet('meatWeight', 0)
            ->assertSet('useGrinding', false)
            ->set('startType', 'start_from_zero')
            ->assertSet('meatWeight', 500)
            ->assertSet('useGrinding', true);
    }

    public function test_calculator_blocks_checkout_when_selected_core_material_is_unavailable(): void
    {
        Material::where('name', 'Daging Sapi')->firstOrFail()->update(['is_available' => false]);
        $pickupDate = now()->addDay()->setTime(8, 0)->format('Y-m-d\TH:i');

        Livewire::test(RecipeCalculator::class)
            ->set('startType', 'start_from_zero')
            ->set('meatType', 'sapi')
            ->assertSee('Stok Bahan Sedang Kosong')
            ->call('openCheckout')
            ->assertSet('showCheckoutModal', false)
            ->set('customerName', 'Pak Ahmad')
            ->set('customerPhone', '081234567890')
            ->set('pickupTime', $pickupDate)
            ->call('processCheckout')
            ->assertHasErrors(['calculation']);

        $this->assertDatabaseMissing('orders', ['customer_name' => 'Pak Ahmad']);
    }

    public function test_extra_materials_do_not_leak_raw_blade_syntax(): void
    {
        Livewire::test(RecipeCalculator::class)
            ->assertDontSee('{{ number_format', false)
            ->assertSee('@60g', false);
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
        $tapioka = Material::where('name', 'Tepung Tapioka')->firstOrFail();
        $garlic = Material::where('name', 'Bawang Putih Kupas')->firstOrFail();

        Livewire::test(RecipeCalculator::class)
            ->set('startType', 'ingredients_only')
            ->call('incrementExtra', $tapioka->id)
            ->call('incrementExtra', $tapioka->id)
            ->call('incrementExtra', $garlic->id)
            ->assertSet('meatWeight', 0)
            ->assertSet('useGrinding', false)
            ->assertSee('Beli Bahan Saja')
            ->assertSee('Tanpa Daging');
    }

    public function test_calculator_loads_transferred_materials_from_session(): void
    {
        $material = Material::where('name', 'Merica Bubuk Sachet')->first();
        session(['selected_materials' => [$material->id => 3]]);

        Livewire::test(RecipeCalculator::class)
            ->assertSet('extraItems.'.$material->id, 3)
            ->assertSee('Merica Bubuk Sachet')
            ->assertSee('Pelengkap Kios');
    }

    public function test_calculator_can_increment_and_decrement_extra_materials(): void
    {
        $material = Material::where('name', 'Telur Ayam')->first();

        Livewire::test(RecipeCalculator::class)
            ->call('incrementExtra', $material->id)
            ->assertSet('extraItems.'.$material->id, 1)
            ->assertSee('Telur Ayam')
            ->call('incrementExtra', $material->id)
            ->assertSet('extraItems.'.$material->id, 2)
            ->call('decrementExtra', $material->id)
            ->assertSet('extraItems.'.$material->id, 1)
            ->call('decrementExtra', $material->id)
            ->assertSet('extraItems', []);
    }

    public function test_ingredients_only_mode_does_not_force_flour_and_blocks_empty_checkout(): void
    {
        $egg = Material::where('name', 'Telur Ayam')->first();
        $pepper = Material::where('name', 'Merica Bubuk Sachet')->first();

        // 1. Pindah ke Beli Bahan Saja -> tepung tidak dipaksa (0 item) & checkout diblokir sampai pilih bahan
        $component = Livewire::test(RecipeCalculator::class)
            ->set('startType', 'ingredients_only')
            ->assertSet('breakdown', [])
            ->assertSet('totalMarketCost', 0.0)
            ->assertSee('Belum Ada Bahan Dipilih')
            ->call('openCheckout')
            ->assertSet('showCheckoutModal', false);

        // 2. Pilih hanya Telur Ayam & Merica Bubuk tanpa tepung -> breakdown hanya berisi 2 item tersebut
        $component->call('incrementExtra', $egg->id)
            ->call('incrementExtra', $pepper->id)
            ->assertSet('totalMarketCost', 3500.0)
            ->assertSee('Telur Ayam')
            ->assertSee('Merica Bubuk Sachet')
            ->call('openCheckout')
            ->assertSet('showCheckoutModal', true);

        $itemNames = collect($component->get('breakdown'))->pluck('name')->all();
        $this->assertEqualsCanonicalizing(['Telur Ayam', 'Merica Bubuk Sachet'], $itemNames);
    }

    public function test_calculator_step_5_displays_live_ratios_from_admin_database(): void
    {
        RecipeFormula::updateOrCreate(
            ['code' => 'enak'],
            [
                'name' => 'Racikan Enak (Spesial Daging)',
                'meat_ratio' => 1.0,
                'flour_ratio' => 0.2,
                'spice_ratio' => 0.05,
                'is_simulation' => false,
                'description' => 'Rasio admin 5 banding 1.',
            ]
        );

        Livewire::test(RecipeCalculator::class)
            ->assertSee('20% Tepung')
            ->assertSee('Daging 5 : 1 Tepung')
            ->assertSee('1 kg daging + 200g tepung')
            ->assertSee('Rasio admin 5 banding 1.');
    }

    public function test_custom_flour_ratio_supports_up_to_450_percent_and_persists_in_wa_and_saved_recipe(): void
    {
        $user = User::factory()->create();
        $pickupDate = now()->addDays(2)->setTime(8, 0)->format('Y-m-d\TH:i');

        // 1. 1 kg daging + 350% tepung (cilok / bakso aci 1:3,5) -> 3.500g tepung (7 pack @500g)
        $component = Livewire::actingAs($user)
            ->test(RecipeCalculator::class)
            ->set('meatWeight', 1000)
            ->set('preference', 'kustom')
            ->set('customFlourRatio', 350)
            ->assertSee('1 kg daging + 3,5 kg tepung')
            ->assertSee('Bakso Aci / Cilok Daging');

        $tapioka = collect($component->get('breakdown'))->firstWhere('name', 'Tepung Tapioka');
        $this->assertEquals(3500.0, (float) $tapioka['weight']);
        $this->assertEquals(63000.0, (float) $tapioka['market_cost']); // 7 pack * Rp 9.000

        // 2. Pastikan default nama simpan resep menyertakan angka % kustom
        $component->call('openSaveModal');
        $this->assertStringContainsString('Kustom 350%', (string) $component->get('recipeName'));
        $component->call('saveRecipe')->assertHasNoErrors();

        $saved = SavedRecipe::where('user_id', $user->id)->latest('id')->firstOrFail();

        // 3. Reload saved recipe -> rasio 350% tidak terpotong di 50%
        Livewire::actingAs($user)
            ->withQueryParams(['recipe_id' => $saved->id])
            ->test(RecipeCalculator::class)
            ->assertSet('preference', 'kustom')
            ->assertSet('customFlourRatio', 350);

        // 4. Pesan WA memuat rasio kustom aktual (Kustom (350% Tepung))
        $checkout = Livewire::actingAs($user)
            ->test(RecipeCalculator::class)
            ->set('meatWeight', 1000)
            ->set('preference', 'kustom')
            ->set('customFlourRatio', 350)
            ->call('openCheckout')
            ->set('customerName', 'Pedagang Cilok')
            ->set('customerPhone', '081234567890')
            ->set('pickupTime', $pickupDate)
            ->call('processCheckout')
            ->assertHasNoErrors();

        $waUrl = (string) $checkout->get('confirmedWaUrl');
        $this->assertStringContainsString('Kustom (350% Tepung)', urldecode($waUrl));
    }

    public function test_curah_seasonings_round_cash_without_forcing_whole_packs_while_flour_stays_packaged(): void
    {
        $component = Livewire::test(RecipeCalculator::class)
            ->set('meatWeight', 500);

        $breakdown = collect($component->get('breakdown'));
        $tapioka = $breakdown->firstWhere('name', 'Tepung Tapioka');
        $garlic = $breakdown->firstWhere('name', 'Bawang Putih Kupas');
        $salt = $breakdown->firstWhere('name', 'Garam Kasar');

        // Tepung tetap wajib kemasan utuh (1 Pack @500g = Rp 9.000, sisa 400g)
        $this->assertSame(9000.0, (float) $tapioka['market_cost']);
        $this->assertSame(400, (int) $tapioka['excess_grams']);

        // Bawang Putih Kupas curah (15g @ Rp40 = Rp600 -> bulat tunai Rp 1.000, tanpa sisa pack)
        $this->assertSame(1000.0, (float) $garlic['market_cost']);
        $this->assertSame(0, (int) $garlic['excess_grams']);
        $this->assertSame('Timbang pas los', $garlic['package_info']);

        // Garam Kasar curah (10g @ Rp6 = Rp60 -> minimal tunai Rp 500, tanpa paksa 1kg)
        $this->assertSame(500.0, (float) $salt['market_cost']);
        $this->assertSame(0, (int) $salt['excess_grams']);
        $this->assertSame('Timbang pas los', $salt['package_info']);
    }

    public function test_clearing_numeric_inputs_to_empty_string_does_not_throw_error(): void
    {
        Livewire::test(RecipeCalculator::class)
            ->set('meatWeight', '')
            ->assertHasNoErrors()
            ->assertSet('totalProportionalCost', 79960.0)
            ->set('customFlourRatio', '')
            ->assertHasNoErrors()
            ->assertSet('totalProportionalCost', 79960.0)
            ->set('calculationMode', 'budget')
            ->set('budget', '')
            ->assertHasNoErrors()
            ->assertSet('totalProportionalCost', 0.0)
            ->set('startType', 'ingredients_only')
            ->assertHasNoErrors()
            ->assertSet('totalMarketCost', 0.0);
    }

    public function test_budget_mode_handles_huge_budget_without_overflow(): void
    {
        Livewire::test(RecipeCalculator::class)
            ->set('startType', 'own_meat')
            ->set('useGrinding', false)
            ->set('calculationMode', 'budget')
            ->set('budget', '999999999999999')
            ->assertSet('budgetError', null)
            ->assertSet('meatWeight', 50000);
    }

    public function test_transferring_daging_from_catalog_maps_to_main_meat_without_double_charging(): void
    {
        $chicken = Material::where('name', 'Daging Ayam')->firstOrFail();
        $egg = Material::where('name', 'Telur Ayam')->firstOrFail();

        session(['selected_materials' => [
            $chicken->id => 2, // 2 x 1000g (kemasan 1kg) = 2000g daging ayam
            $egg->id => 1,
        ]]);

        $component = Livewire::test(RecipeCalculator::class)
            ->assertSet('meatType', 'ayam')
            ->assertSet('meatWeight', 2000)
            ->assertSet('extraItems', [$egg->id => 1]);

        $chickenRows = collect($component->get('breakdown'))->where('name', 'Daging Ayam');
        $this->assertCount(1, $chickenRows);
    }

    public function test_custom_mode_dynamically_scales_seasoning_with_flour_ratio(): void
    {
        // 20% tepung -> 5% bumbu (25g total untuk 500g daging: 15g bawang, 10g garam)
        $low = Livewire::test(RecipeCalculator::class)
            ->set('meatWeight', 500)
            ->set('preference', 'kustom')
            ->set('customFlourRatio', 20);
        $lowGarlic = collect($low->get('breakdown'))->firstWhere('name', 'Bawang Putih Kupas');
        $this->assertEquals(15.0, (float) $lowGarlic['weight']);

        // 200% tepung -> 12% bumbu (60g total untuk 500g daging: 36g bawang, 24g garam)
        $mid = Livewire::test(RecipeCalculator::class)
            ->set('meatWeight', 500)
            ->set('preference', 'kustom')
            ->set('customFlourRatio', 200);
        $midGarlic = collect($mid->get('breakdown'))->firstWhere('name', 'Bawang Putih Kupas');
        $this->assertEquals(36.0, (float) $midGarlic['weight']);

        // 350% tepung (cilok) -> 18% bumbu (90g total untuk 500g daging: 54g bawang, 36g garam)
        $high = Livewire::test(RecipeCalculator::class)
            ->set('meatWeight', 500)
            ->set('preference', 'kustom')
            ->set('customFlourRatio', 350);
        $highGarlic = collect($high->get('breakdown'))->firstWhere('name', 'Bawang Putih Kupas');
        $this->assertEquals(54.0, (float) $highGarlic['weight']);
    }

    public function test_floating_point_pack_and_cash_rounding_does_not_overcharge(): void
    {
        // 12500g daging * 220% tepung = tepat 27500g = tepat 55 pack @500g (Rp 495.000, bukan 56 pack)
        $component = Livewire::test(RecipeCalculator::class)
            ->set('meatWeight', 12500)
            ->set('preference', 'kustom')
            ->set('customFlourRatio', 220);

        $tapioka = collect($component->get('breakdown'))->firstWhere('name', 'Tepung Tapioka');
        $this->assertEquals(27500.0, (float) $tapioka['weight']);
        $this->assertEquals(495000.0, (float) $tapioka['market_cost']);

        // 12500g daging * 5% bumbu * 40% garam = 250g * Rp 6 = tepat Rp 1.500 (bukan Rp 2.000)
        $saltComponent = Livewire::test(RecipeCalculator::class)
            ->set('meatWeight', 12500)
            ->set('preference', 'kustom')
            ->set('customFlourRatio', 20);

        $salt = collect($saltComponent->get('breakdown'))->firstWhere('name', 'Garam Kasar');
        $this->assertEquals(1500.0, (float) $salt['market_cost']);
    }

    public function test_ingredients_only_checkout_persists_bahan_saja_and_accurate_pack_order_items(): void
    {
        $tapioka = Material::where('name', 'Tepung Tapioka')->firstOrFail();
        $egg = Material::where('name', 'Telur Ayam')->firstOrFail();
        $pickupDate = now()->addDay()->setTime(8, 0)->format('Y-m-d\TH:i');

        Livewire::test(RecipeCalculator::class)
            ->set('startType', 'ingredients_only')
            ->call('incrementExtra', $tapioka->id)
            ->call('incrementExtra', $tapioka->id)
            ->call('incrementExtra', $egg->id)
            ->call('incrementExtra', $egg->id)
            ->call('incrementExtra', $egg->id)
            ->call('openCheckout')
            ->set('customerName', 'Ibu Warteg')
            ->set('customerPhone', '081234567890')
            ->set('pickupTime', $pickupDate)
            ->call('processCheckout')
            ->assertHasNoErrors();

        $order = Order::where('customer_name', 'Ibu Warteg')->firstOrFail();
        $this->assertSame('bahan_saja', $order->preference);
        $this->assertEquals(25500.0, (float) $order->total_price);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'material_name' => 'Tepung Tapioka',
            'quantity' => 2.000,
            'unit_price' => 9000.00,
            'subtotal' => 18000.00,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'material_name' => 'Telur Ayam',
            'quantity' => 3.000,
            'unit_price' => 2500.00,
            'subtotal' => 7500.00,
        ]);
    }

    public function test_unavailable_seasoning_or_probakso_blocks_checkout_without_silent_fallback(): void
    {
        Material::where('name', 'Bawang Putih Kupas')->firstOrFail()->update(['is_available' => false]);

        Livewire::test(RecipeCalculator::class)
            ->set('startType', 'start_from_zero')
            ->assertSee('Stok Bahan Sedang Kosong')
            ->call('openCheckout')
            ->assertSet('showCheckoutModal', false);

        Material::where('name', 'Bawang Putih Kupas')->firstOrFail()->update(['is_available' => true]);
        Material::where('name', 'Tepung ProBakso')->firstOrFail()->update(['is_available' => false]);

        $probaksoTest = Livewire::test(RecipeCalculator::class)
            ->set('flourType', 'probakso')
            ->assertSee('Stok Bahan Sedang Kosong')
            ->call('openCheckout')
            ->assertSet('showCheckoutModal', false);

        $names = collect($probaksoTest->get('breakdown'))->pluck('name')->all();
        $this->assertNotContains('Tepung Tapioka', $names);
    }

    public function test_malformed_pickup_time_fails_validation_cleanly_without_exception(): void
    {
        Livewire::test(RecipeCalculator::class)
            ->set('meatWeight', 500)
            ->call('openCheckout')
            ->set('customerName', 'Pelanggan Tes')
            ->set('customerPhone', '081234567890')
            ->set('pickupTime', 'bukan-tanggal-valid')
            ->call('processCheckout')
            ->assertHasErrors(['pickupTime']);
    }

    public function test_own_meat_checkout_stores_zero_unit_price_and_subtotal_for_customer_meat(): void
    {
        $validPickup = now()->addDay()->setTime(9, 0)->format('Y-m-d\TH:i');

        Livewire::test(RecipeCalculator::class)
            ->set('startType', 'own_meat')
            ->set('meatType', 'sapi')
            ->set('meatWeight', 1000)
            ->set('preference', 'enak')
            ->call('openCheckout')
            ->set('customerName', 'Bu Dewi Own Meat')
            ->set('customerPhone', '081234567811')
            ->set('pickupTime', $validPickup)
            ->call('processCheckout')
            ->assertHasNoErrors();

        $order = Order::where('customer_name', 'Bu Dewi Own Meat')->firstOrFail();
        $this->assertSame('own_meat', $order->start_type);
        $this->assertTrue($order->isOwnMeat());

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'material_name' => 'Daging Sapi',
            'quantity' => 1000.000,
            'unit_price' => 0.00,
            'subtotal' => 0.00,
        ]);
    }

    public function test_checkout_accepts_and_normalizes_formatted_whatsapp_phone_numbers(): void
    {
        $validPickup = now()->addDay()->setTime(9, 0)->format('Y-m-d\TH:i');

        $component = Livewire::test(RecipeCalculator::class)
            ->set('meatWeight', 500)
            ->call('openCheckout')
            ->set('customerName', 'Pak Agus Format WA')
            ->set('customerPhone', '0812-3456 7890')
            ->set('pickupTime', $validPickup)
            ->call('processCheckout')
            ->assertHasNoErrors()
            ->assertSet('customerPhone', '081234567890')
            ->assertSet('showCheckoutModal', true);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Pak Agus Format WA',
            'customer_phone' => '081234567890',
        ]);
        $this->assertStringContainsString('081234567890', urldecode((string) $component->get('confirmedWaUrl')));
    }

    public function test_transferring_only_non_meat_items_from_catalog_switches_to_ingredients_only_without_double_flour(): void
    {
        $tapioka = Material::where('name', 'Tepung Tapioka')->firstOrFail();
        $pepper = Material::where('name', 'Merica Bubuk Sachet')->firstOrFail();

        session(['selected_materials' => [
            $tapioka->id => 2,
            $pepper->id => 1,
        ]]);

        $component = Livewire::test(RecipeCalculator::class)
            ->assertSet('startType', 'ingredients_only')
            ->assertSet('meatWeight', 0)
            ->assertSet('useGrinding', false)
            ->assertSet('extraItems', [
                $tapioka->id => 2,
                $pepper->id => 1,
            ]);

        $breakdown = collect($component->get('breakdown'));
        $this->assertCount(1, $breakdown->where('name', 'Tepung Tapioka'));
        $this->assertCount(1, $breakdown->where('name', 'Merica Bubuk Sachet'));
        $this->assertCount(0, $breakdown->where('name', 'Daging Sapi'));
    }

    public function test_transferring_meat_and_flour_from_catalog_maps_primary_flour_without_duplication(): void
    {
        $chicken = Material::where('name', 'Daging Ayam')->firstOrFail();
        $probakso = Material::where('name', 'Tepung ProBakso')->firstOrFail();
        $egg = Material::where('name', 'Telur Ayam')->firstOrFail();

        session(['selected_materials' => [
            $chicken->id => 2,
            $probakso->id => 1,
            $egg->id => 1,
        ]]);

        $component = Livewire::test(RecipeCalculator::class)
            ->assertSet('startType', 'start_from_zero')
            ->assertSet('meatType', 'ayam')
            ->assertSet('meatWeight', 2000)
            ->assertSet('flourType', 'probakso')
            ->assertSet('extraItems', [$egg->id => 1]);

        $breakdown = collect($component->get('breakdown'));
        $this->assertCount(1, $breakdown->where('name', 'Tepung ProBakso'));
        $this->assertCount(0, $breakdown->where('name', 'Tepung Tapioka'));
    }

    public function test_manual_numeric_inputs_bind_on_blur_instead_of_live_debounce_to_prevent_mid_typing_overwrite(): void
    {
        $html = Livewire::test(RecipeCalculator::class)
            ->set('preference', 'kustom')
            ->html();

        $this->assertStringContainsString('wire:model.live.blur="meatWeight"', $html);
        $this->assertStringNotContainsString('wire:model.blur="meatWeight"', $html);
        $this->assertStringNotContainsString('wire:model.live.debounce.300ms="meatWeight"', $html);
        $this->assertStringContainsString('wire:model.live.blur="customFlourRatio"', $html);
        $this->assertStringNotContainsString('wire:model.blur="customFlourRatio"', $html);
        $this->assertStringNotContainsString('wire:model.live.debounce.200ms="customFlourRatio"', $html);
    }
}
