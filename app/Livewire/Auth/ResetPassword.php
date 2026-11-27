<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Atur Ulang Kata Sandi — BASOKU')]
class ResetPassword extends Component
{
    #[Locked]
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $queryEmail = request()->query('email');
        $this->email = is_string($queryEmail) ? Str::lower(trim($queryEmail)) : '';
    }

    /**
     * Validasi token reset dan perbarui kata sandi pengguna secara aman.
     */
    public function resetPassword()
    {
        $this->email = Str::lower(trim($this->email));

        $throttleKey = 'reset-password|'.request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->resetErrorBag();
            $this->addError('email', "Terlalu banyak percobaan reset kata sandi. Harap tunggu {$seconds} detik.");

            return;
        }

        $this->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'string', 'min:8', 'max:128', 'confirmed'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        RateLimiter::hit($throttleKey, 300);

        $status = Password::reset(
            [
                'token' => $this->token,
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
            ],
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            RateLimiter::clear($throttleKey);
            session()->flash('status', 'Kata sandi Anda berhasil diperbarui. Silakan masuk dengan kata sandi baru.');

            return $this->redirect(route('login'), navigate: true);
        }

        $this->addError('email', 'Tautan atur ulang kata sandi tidak valid atau sudah kedaluwarsa. Silakan ajukan tautan baru.');
    }

    public function render(): View
    {
        return view('livewire.auth.reset-password');
    }
}
