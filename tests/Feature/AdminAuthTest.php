<?php

namespace Tests\Feature;

use App\Livewire\Admin\Dashboard;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminUserSeeder::class);
    }

    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/masuk');

        $response->assertStatus(200);
        $response->assertSeeLivewire(Login::class);
        $response->assertSee('Pintu Masuk Admin');
    }

    public function test_unauthenticated_user_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/masuk');
    }

    public function test_admin_can_authenticate_and_redirect_to_dashboard(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'admin@basoku.com')
            ->set('password', 'password')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect('/admin/dashboard');

        $this->assertTrue(Auth::check());
        $this->assertEquals('admin', Auth::user()->role);
    }

    public function test_authentication_fails_with_invalid_credentials(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'admin@basoku.com')
            ->set('password', 'wrong-password')
            ->call('authenticate')
            ->assertHasErrors(['email']);

        $this->assertFalse(Auth::check());
    }

    public function test_customer_role_redirects_to_home_upon_login(): void
    {
        User::create([
            'name' => 'Pelanggan Setia',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        Livewire::test(Login::class)
            ->set('email', 'customer@gmail.com')
            ->set('password', 'password')
            ->call('authenticate')
            ->assertRedirect('/');

        $this->assertTrue(Auth::check());
        $this->assertEquals('customer', Auth::user()->role);
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = User::create([
            'name' => 'Pelanggan Toko',
            'email' => 'customer2@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        $response = $this->actingAs($customer)->get('/admin/dashboard');

        // Customer should be redirected back away from admin dashboard
        $response->assertRedirect('/masuk');
    }

    public function test_authenticated_admin_can_view_dashboard(): void
    {
        $admin = User::where('email', 'admin@basoku.com')->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSeeLivewire(Dashboard::class);
        $response->assertSee('Selamat Datang di Dasbor Admin BASOKU');
        $response->assertSee('toggleDesktopSidebar', false);
        $response->assertSee('toggleMobileMenu', false);
    }

    public function test_admin_can_logout_via_dashboard(): void
    {
        $admin = User::where('email', 'admin@basoku.com')->first();

        Livewire::actingAs($admin)
            ->test(Dashboard::class)
            ->call('logout')
            ->assertRedirect('/masuk');

        $this->assertFalse(Auth::check());
    }

    public function test_auth_normalizes_email_case_and_blocks_role_mass_assignment(): void
    {
        // 1. Mass-assignment role => admin harus terabaikan (tetap default 'customer')
        $massAssigned = User::create([
            'name' => 'Mass Assign Check',
            'email' => 'mass-check@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
        $this->assertSame('customer', $massAssigned->fresh()->role);

        // 2. Registrasi dengan huruf besar + spasi dinormalisasi menjadi huruf kecil
        Livewire::test(Register::class)
            ->set('name', '  Siti Rahayu  ')
            ->set('email', '  Siti.Rahayu@Example.COM  ')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'name' => 'Siti Rahayu',
            'email' => 'siti.rahayu@example.com',
            'role' => 'customer',
        ]);

        Auth::logout();

        // 3. Registrasi ulang dengan beda kapitalisasi harus ditolak
        Livewire::test(Register::class)
            ->set('name', 'Duplikat')
            ->set('email', 'SITI.RAHAYU@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['email']);

        // 4. Login tetap berhasil dengan variasi kapitalisasi dan spasi
        Livewire::test(Login::class)
            ->set('email', '  SITI.RAHAYU@EXAMPLE.COM ')
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect('/');
    }

    public function test_admin_dashboard_tracks_kustom_top_preference_and_dibatalkan_status(): void
    {
        $admin = User::where('email', 'admin@basoku.com')->firstOrFail();

        Order::create([
            'customer_name' => 'Pembeli Kustom 1',
            'pickup_at' => now()->addDay(),
            'start_type' => 'start_from_zero',
            'meat_weight' => 1000,
            'preference' => 'kustom',
            'grinding_fee' => 15000,
            'total_price' => 175000,
            'status' => 'completed',
        ]);

        Order::create([
            'customer_name' => 'Pembeli Kustom 2',
            'pickup_at' => now()->addDay(),
            'start_type' => 'start_from_zero',
            'meat_weight' => 1000,
            'preference' => 'kustom',
            'grinding_fee' => 15000,
            'total_price' => 175000,
            'status' => 'dibatalkan',
        ]);

        Livewire::actingAs($admin)
            ->test(Dashboard::class)
            ->assertSee('Kustom')
            ->assertViewHas('topPreference', fn (array $top) => ($top['label'] ?? null) === 'Kustom' && ($top['count'] ?? 0) === 2)
            ->assertViewHas('cancelledCount', 1);
    }
}
