<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Gerbang Autentikasi Tamu (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('/masuk', Login::class)->name('login');
    Route::redirect('/login', '/masuk');
    Route::get('/daftar', Register::class)->name('register');
    Route::redirect('/register', '/daftar');
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
