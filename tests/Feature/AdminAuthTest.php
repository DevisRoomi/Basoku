<?php

namespace Tests\Feature;

use App\Livewire\Admin\Dashboard;
use App\Livewire\Auth\Login;
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
}
