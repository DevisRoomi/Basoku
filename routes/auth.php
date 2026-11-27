<?php

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Gerbang Autentikasi Tamu (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('/masuk', Login::class)->name('login');
    Route::redirect('/login', '/masuk');
    Route::get('/daftar', Register::class)->name('register');
    Route::redirect('/register', '/daftar');
    Route::get('/lupa-kata-sandi', ForgotPassword::class)->name('password.request');
    Route::get('/reset-kata-sandi/{token}', ResetPassword::class)->name('password.reset');
});

// Logout Global (Auth Only)
Route::middleware('auth')->group(function () {
    Route::post('/keluar', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/masuk');
    })->name('logout');
});
