<?php

namespace Tests\Feature;

use App\Livewire\Account\ManageAccount;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityAndAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_responses_include_production_security_headers(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()')
            ->assertHeaderMissing('Strict-Transport-Security');

        $httpsResponse = $this->get('https://basoku.test/masuk');

        $httpsResponse->assertStatus(200)
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    public function test_custom_error_pages_render_basoku_branding_without_framework_defaults(): void
    {
        $response = $this->get('/halaman-tidak-ditemukan-basoku-test');

        $response->assertStatus(404)
            ->assertSee('Kode 404')
            ->assertSee('Halaman Tidak Ditemukan')
            ->assertSee('Kembali ke Beranda')
            ->assertSee('BASOKU');

        foreach (['403', '404', '419', '500'] as $code) {
            $rendered = view("errors.{$code}")->render();
            $this->assertStringContainsString("Kode {$code}", $rendered);
            $this->assertStringContainsString('BASOKU', $rendered);
        }
    }

    public function test_guest_can_request_password_reset_link_without_email_enumeration(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'pelanggan@example.com',
        ]);

        $this->get('/lupa-kata-sandi')
            ->assertStatus(200)
            ->assertSeeLivewire(ForgotPassword::class);

        $existing = Livewire::test(ForgotPassword::class)
            ->set('email', '  PELANGGAN@Example.com ')
            ->call('sendResetLink')
            ->assertHasNoErrors();

        Notification::assertSentTo($user, ResetPasswordNotification::class);

        $unknown = Livewire::test(ForgotPassword::class)
            ->set('email', 'tidak-terdaftar@example.com')
            ->call('sendResetLink')
            ->assertHasNoErrors();

        $this->assertNotEmpty($existing->get('statusMessage'));
        $this->assertSame($existing->get('statusMessage'), $unknown->get('statusMessage'));
    }

    public function test_guest_can_reset_password_with_valid_token_and_invalid_token_is_rejected(): void
    {
        $user = User::factory()->create([
            'email' => 'reset@example.com',
            'password' => Hash::make('sandiLama123'),
        ]);

        $token = Password::broker()->createToken($user);

        $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->assertStatus(200)
            ->assertSeeLivewire(ResetPassword::class);

        Livewire::test(ResetPassword::class, ['token' => 'token-palsu-tidak-valid'])
            ->set('email', 'reset@example.com')
            ->set('password', 'sandiBaru123')
            ->set('password_confirmation', 'sandiBaru123')
            ->call('resetPassword')
            ->assertHasErrors(['email']);

        $this->assertTrue(Hash::check('sandiLama123', (string) $user->fresh()->password));

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', 'reset@example.com')
            ->set('password', 'sandiBaru123')
            ->set('password_confirmation', 'sandiBaru123')
            ->call('resetPassword')
            ->assertHasNoErrors()
            ->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('sandiBaru123', (string) $user->fresh()->password));
        $this->assertFalse(Hash::check('sandiLama123', (string) $user->fresh()->password));
    }

    public function test_authenticated_customer_and_admin_can_update_profile_and_password_safely(): void
    {
        $this->get('/pengaturan-akun')->assertRedirect('/masuk');

        $customer = User::factory()->create([
            'name' => 'Pelanggan Lama',
            'email' => 'lama@example.com',
            'password' => Hash::make('rahasia123'),
            'role' => 'customer',
        ]);

        $this->actingAs($customer)
            ->get('/pengaturan-akun')
            ->assertStatus(200)
            ->assertSeeLivewire(ManageAccount::class)
            ->assertSee('Pengaturan Akun');

        Livewire::actingAs($customer)
            ->test(ManageAccount::class)
            ->set('name', '  Pelanggan Baru  ')
            ->set('email', '  BARU@Example.COM ')
            ->call('updateProfile')
            ->assertHasNoErrors()
            ->assertSee('berhasil diperbarui');

        $customer->refresh();
        $this->assertSame('Pelanggan Baru', $customer->name);
        $this->assertSame('baru@example.com', $customer->email);
        $this->assertSame('customer', $customer->role);

        Livewire::actingAs($customer)
            ->test(ManageAccount::class)
            ->set('current_password', 'salah-total')
            ->set('password', 'sandiBaru999')
            ->set('password_confirmation', 'sandiBaru999')
            ->call('updatePassword')
            ->assertHasErrors(['current_password']);

        Livewire::actingAs($customer)
            ->test(ManageAccount::class)
            ->set('current_password', 'rahasia123')
            ->set('password', 'sandiBaru999')
            ->set('password_confirmation', 'sandiBaru999')
            ->call('updatePassword')
            ->assertHasNoErrors()
            ->assertSee('Kata sandi Anda berhasil diubah.');

        $this->assertTrue(Hash::check('sandiBaru999', (string) $customer->fresh()->password));

        $admin = User::factory()->create([
            'name' => 'Admin Kios',
            'email' => 'admin-settings@basoku.com',
            'password' => Hash::make('adminLama123'),
        ]);
        $admin->forceFill(['role' => 'admin'])->save();

        $this->actingAs($admin)
            ->get('/pengaturan-akun')
            ->assertStatus(200)
            ->assertSee('Pengaturan Akun &amp; Ganti Kata Sandi', false);

        Livewire::actingAs($admin)
            ->test(ManageAccount::class)
            ->set('current_password', 'adminLama123')
            ->set('password', 'adminBaru999')
            ->set('password_confirmation', 'adminBaru999')
            ->call('updatePassword')
            ->assertHasNoErrors();

        $this->assertTrue(Hash::check('adminBaru999', (string) $admin->fresh()->password));
    }

    public function test_auth_and_account_forms_render_password_visibility_toggle_and_throttle_failed_password_changes(): void
    {
        $this->get('/masuk')
            ->assertStatus(200)
            ->assertSee('Tampilkan kata sandi');

        $this->get('/daftar')
            ->assertStatus(200)
            ->assertSee('Tampilkan kata sandi');

        $customer = User::factory()->create([
            'password' => Hash::make('rahasia123'),
            'role' => 'customer',
        ]);

        $this->actingAs($customer)
            ->get('/pengaturan-akun')
            ->assertStatus(200)
            ->assertSee('Tampilkan kata sandi');

        // Percobaan current_password salah 10x wajib memicu throttle pada percobaan ke-11
        $component = Livewire::actingAs($customer)->test(ManageAccount::class);
        for ($i = 0; $i < 10; $i++) {
            $component->set('current_password', 'salah-terus')
                ->set('password', 'sandiBaru999')
                ->set('password_confirmation', 'sandiBaru999')
                ->call('updatePassword')
                ->assertHasErrors(['current_password']);
        }

        $component->set('current_password', 'rahasia123')
            ->set('password', 'sandiBaru999')
            ->set('password_confirmation', 'sandiBaru999')
            ->call('updatePassword')
            ->assertSee('Terlalu banyak percobaan ubah sandi');
    }
}
