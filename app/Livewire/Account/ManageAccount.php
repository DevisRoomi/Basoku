<?php

namespace App\Livewire\Account;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ManageAccount extends Component
{
    public string $name = '';

    public string $email = '';

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?string $profileNotice = null;

    public ?string $passwordNotice = null;

    public function boot(): void
    {
        if (! Auth::check()) {
            abort(403);
        }
    }

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->name = (string) $user->name;
        $this->email = (string) $user->email;
    }

    /**
     * Perbarui nama dan alamat email pengguna terautentikasi tanpa menyentuh kolom role.
     */
    public function updateProfile(): void
    {
        $this->profileNotice = null;
        $this->name = trim($this->name);
        $this->email = Str::lower(trim($this->email));

        /** @var User $user */
        $user = Auth::user();

        $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama lengkap minimal 2 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email tersebut sudah digunakan oleh akun lain.',
        ]);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        $this->profileNotice = 'Informasi profil akun Anda berhasil diperbarui.';
    }

    /**
     * Ubah kata sandi akun setelah memverifikasi kata sandi saat ini.
     */
    public function updatePassword(): void
    {
        $this->passwordNotice = null;

        /** @var User $user */
        $user = Auth::user();
        $throttleKey = 'change-password|'.$user->id.'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->resetErrorBag();
            $this->addError('current_password', "Terlalu banyak percobaan ubah sandi. Harap tunggu {$seconds} detik.");

            return;
        }

        // Hit sebelum validasi agar percobaan tebak current_password yang gagal benar-benar dihitung oleh RateLimiter
        RateLimiter::hit($throttleKey, 300);

        $this->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'max:128', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.different' => 'Kata sandi baru harus berbeda dari kata sandi saat ini.',
        ]);

        $user->forceFill([
            'password' => Hash::make($this->password),
            'remember_token' => Str::random(60),
        ])->save();

        session()->regenerate();
        RateLimiter::clear($throttleKey);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->passwordNotice = 'Kata sandi Anda berhasil diubah.';
    }

    public function render(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $isAdmin = $user->isAdmin();

        return view('livewire.account.manage-account', [
            'isAdmin' => $isAdmin,
        ])
            ->layout($isAdmin ? 'components.layouts.admin' : 'components.layouts.app')
            ->title('Pengaturan Akun & Keamanan — BASOKU');
    }
}
