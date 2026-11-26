<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Masuk — BASOKU')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    /**
     * Autentikasi kredensial pengguna.
     */
    public function authenticate()
    {
        $this->email = Str::lower(trim($this->email));

        $this->validate([
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'string', 'max:128'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $throttleKey = Str::transliterate($this->email.'|'.request()->ip());
        $ipThrottleKey = 'login-ip|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5) || RateLimiter::tooManyAttempts($ipThrottleKey, 20)) {
            $seconds = max(RateLimiter::availableIn($throttleKey), RateLimiter::availableIn($ipThrottleKey));
            $this->addError('email', "Terlalu banyak percobaan masuk. Harap tunggu {$seconds} detik sebelum mencoba kembali.");

            return;
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::clear($throttleKey);
            session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return $this->redirect('/admin/dashboard', navigate: true);
            }

            if (session()->has('calculator_draft')) {
                return $this->redirect('/kalkulator', navigate: true);
            }

            return $this->redirect('/', navigate: true);
        }

        RateLimiter::hit($throttleKey, 60);
        RateLimiter::hit($ipThrottleKey, 300);

        $this->addError('email', 'Email atau kata sandi yang Anda masukkan salah.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
