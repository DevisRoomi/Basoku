<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Lupa Kata Sandi — BASOKU')]
class ForgotPassword extends Component
{
    public string $email = '';

    public ?string $statusMessage = null;

    /**
     * Kirim tautan atur ulang kata sandi ke email terdaftar tanpa membocorkan status keberadaan akun.
     */
    public function sendResetLink(): void
    {
        $this->statusMessage = null;
        $this->email = Str::lower(trim($this->email));

        $this->validate([
            'email' => ['required', 'email', 'max:150'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ]);

        $throttleKey = 'forgot-password|'.request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Terlalu banyak permintaan reset kata sandi. Harap tunggu {$seconds} detik.");

            return;
        }

        RateLimiter::hit($throttleKey, 300);

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_THROTTLED) {
            $this->addError('email', 'Permintaan reset kata sandi terlalu cepat. Silakan tunggu sebentar lalu coba lagi.');

            return;
        }

        // Pesan seragam (anti-user-enumeration) untuk menjaga kerahasiaan daftar akun terdaftar
        $this->statusMessage = 'Jika email tersebut terdaftar di BASOKU, tautan atur ulang kata sandi telah dikirimkan ke kotak masuk Anda. Periksa juga folder Spam atau Promosi.';
        $this->email = '';
    }

    public function render(): View
    {
        return view('livewire.auth.forgot-password');
    }
}
