<?php

namespace Tests\Feature;

use App\Livewire\Admin\ManageKioskSettings;
use App\Livewire\RecipeCalculator;
use App\Models\KioskSetting;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\MaterialSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageKioskSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([AdminUserSeeder::class, MaterialSeeder::class]);
        $this->admin = User::where('email', 'admin@basoku.com')->firstOrFail();
    }

    public function test_non_admin_cannot_access_kiosk_settings(): void
    {
        $this->get('/admin/pengaturan-kios')->assertRedirect('/masuk');

        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer)->get('/admin/pengaturan-kios')->assertRedirect('/masuk');
    }

    public function test_admin_can_update_kiosk_settings_and_calculator_immediately_reflects_new_grinding_fee_and_wa(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/pengaturan-kios')
            ->assertStatus(200)
            ->assertSeeLivewire(ManageKioskSettings::class);

        Livewire::actingAs($this->admin)
            ->test(ManageKioskSettings::class)
            ->set('wa_number', '0812-9988-7766')
            ->set('grinding_fee_per_kg', '20.000')
            ->set('open_hour', 5)
            ->set('close_hour', 14)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('berhasil disimpan');

        $this->assertDatabaseHas('kiosk_settings', ['key' => 'wa_number', 'value' => '6281299887766']);
        $this->assertDatabaseHas('kiosk_settings', ['key' => 'grinding_fee_per_kg', 'value' => '20000']);
        $this->assertDatabaseHas('kiosk_settings', ['key' => 'open_hour', 'value' => '5']);
        $this->assertDatabaseHas('kiosk_settings', ['key' => 'close_hour', 'value' => '14']);

        $this->assertSame('6281299887766', config('services.kiosk.wa_number'));
        $this->assertEquals(20000.0, config('services.kiosk.grinding_fee_per_kg'));

        // Kalkulator langsung memakai tarif selepan baru (Rp 20.000/kg -> 500g = Rp 10.000) & nomor WA baru
        $calc = Livewire::test(RecipeCalculator::class)
            ->set('meatWeight', 500)
            ->set('useGrinding', true);

        $grindingRow = collect($calc->get('breakdown'))->firstWhere('is_service', true);
        $this->assertNotNull($grindingRow);
        $this->assertEquals(10000.0, (float) $grindingRow['market_cost']);
        $this->assertSame('6281299887766', $calc->get('sellerWaNumber'));

        // Pastikan cache terclear & reload dari DB tetap konsisten
        KioskSetting::clearCache();
        KioskSetting::applyToConfig();
        $this->assertSame('6281299887766', config('services.kiosk.wa_number'));
    }

    public function test_kiosk_settings_rejects_invalid_operating_hours_and_wa_number(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ManageKioskSettings::class)
            ->set('wa_number', '123')
            ->set('grinding_fee_per_kg', -500)
            ->set('open_hour', 15)
            ->set('close_hour', 10)
            ->call('save')
            ->assertHasErrors(['wa_number', 'grinding_fee_per_kg', 'close_hour']);
    }
}
