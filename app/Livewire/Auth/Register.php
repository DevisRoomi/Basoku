<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Daftar Akun — BASOKU')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Daftarkan akun baru pelanggan BASOKU.
     */
    public function register()
    {
        $throttleKey = 'register|'.request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Terlalu banyak pendaftaran dari perangkat ini. Harap tunggu {$seconds} detik sebelum mencoba kembali.");

            return;
        }

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan masuk.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        RateLimiter::hit($throttleKey, 300);

        $user = User::create([
            'name' => trim($this->name),
            'email' => strtolower(trim($this->email)),
            'password' => Hash::make($this->password),
            'role' => 'customer',
        ]);

        Auth::login($user);
        session()->regenerate();

        if (session()->has('calculator_draft')) {
            return $this->redirect('/kalkulator', navigate: true);
        }

        return $this->redirect('/racikan-saya', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
