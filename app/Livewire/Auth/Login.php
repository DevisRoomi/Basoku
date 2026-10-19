<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Masuk — Portal Admin BASOKU')]
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
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return $this->redirect('/admin/dashboard', navigate: true);
            }

            return $this->redirect('/', navigate: true);
        }

        $this->addError('email', 'Email atau kata sandi yang Anda masukkan salah.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
