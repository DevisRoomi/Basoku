<?php

namespace Tests\Feature;

use App\Livewire\Admin\Dashboard;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('7°59\'31.5"S 113°14\'58.9"E', false);
    }

    public function test_bakso_history_page_can_be_rendered(): void
    {
        $response = $this->get('/sejarah-bakso');

        $response->assertStatus(200);
        $response->assertSee('Dari Fuzhou ke Los Selepan');
        $response->assertSee('Protein Miosin');
    }

    public function test_calculator_page_can_be_rendered(): void
    {
        $response = $this->get('/kalkulator');

        $response->assertStatus(200);
        $response->assertSee('Rancang Racikanmu');
    }

    public function test_catalog_page_can_be_rendered(): void
    {
        $response = $this->get('/daftar-harga');

        $response->assertStatus(200);
        $response->assertSee('Daftar Harga Transparan');
    }

    public function test_admin_dashboard_returns_ok_and_calculates_valid_revenue_strictly_from_diproses_and_selesai_orders(): void
    {
        $this->seed(AdminUserSeeder::class);
        $admin = User::where('email', 'admin@basoku.com')->first();

        // 1. Order status 'diproses' (harus dihitung ke omzet) -> 100.000
        Order::create([
            'customer_name' => 'Budi Santoso',
            'pickup_at' => now()->addDay(),
            'start_type' => 'start_from_zero',
            'meat_weight' => 500,
            'preference' => 'enak',
            'grinding_fee' => 15000,
            'total_price' => 100000,
            'status' => 'diproses',
        ]);

        // 2. Order status 'selesai' (harus dihitung ke omzet) -> 150.000
        Order::create([
            'customer_name' => 'Siti Rahma',
            'pickup_at' => now()->addDay(),
            'start_type' => 'start_from_zero',
            'meat_weight' => 1000,
            'preference' => 'seimbang',
            'grinding_fee' => 30000,
            'total_price' => 150000,
            'status' => 'selesai',
        ]);

        // 3. Order status 'menunggu' (harus DIABAIKAN dari omzet valid) -> 50.000
        Order::create([
            'customer_name' => 'Agus Pending',
            'pickup_at' => now()->addDay(),
            'start_type' => 'own_meat',
            'meat_weight' => 500,
            'preference' => 'ekonomis',
            'grinding_fee' => 15000,
            'total_price' => 50000,
            'status' => 'menunggu',
        ]);

        // 4. Order status 'batal' (harus DIABAIKAN dari omzet valid) -> 75.000
        Order::create([
            'customer_name' => 'Doni Batal',
            'pickup_at' => now()->addDay(),
            'start_type' => 'start_from_zero',
            'meat_weight' => 500,
            'preference' => 'enak',
            'grinding_fee' => 15000,
            'total_price' => 75000,
            'status' => 'batal',
        ]);

        // Valid Revenue seharusnya HANYA: 100.000 + 150.000 = 250.000
        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Selamat Datang di Dasbor Admin BASOKU');
        $response->assertSee('250.000');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Siti Rahma');

        // Test Livewire instance assertions
        Livewire::actingAs($admin)
            ->test(Dashboard::class)
            ->assertViewHas('validRevenue', 250000.0)
            ->assertViewHas('totalOrders', 4)
            ->assertViewHas('validOrdersCount', 2)
            ->assertViewHas('pendingCount', 1)
            ->assertViewHas('cancelledCount', 1)
            ->assertViewHas('enakCount', 2)
            ->assertViewHas('seimbangCount', 1)
            ->assertViewHas('ekonomisCount', 1);

        // Test route alias /admin/analytics
        $analyticsResponse = $this->actingAs($admin)->get('/admin/analytics');
        $analyticsResponse->assertStatus(200);
        $analyticsResponse->assertSee('Selamat Datang di Dasbor Admin BASOKU');
    }
}
