<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ManageMaterials;
use App\Livewire\Admin\ManageOrders;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Customer\MyRecipeLab;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('/sejarah-bakso', 'bakso-history')->name('bakso.history');
Route::view('/kalkulator', 'calculator-page')->name('calculator');
Route::view('/daftar-harga', 'catalog-page')->name('catalog');

// Gerbang Autentikasi Tamu (Guest Only)
Route::get('/masuk', Login::class)->name('login')->middleware('guest');
Route::redirect('/login', '/masuk');
Route::get('/daftar', Register::class)->name('register')->middleware('guest');
Route::redirect('/register', '/daftar');

// Rute Terproteksi Autentikasi Pengguna & Admin
Route::middleware(['auth'])->group(function () {
    // 1. Laboratorium Resep Pelanggan
    Route::get('/racikan-saya', MyRecipeLab::class)->name('customer.recipes');
    Route::redirect('/resep-saya', '/racikan-saya');

    // 2. Panel Khusus Admin
    Route::get('/admin/dashboard', Dashboard::class)->name('admin.dashboard');
    Route::get('/admin/analytics', Dashboard::class)->name('admin.analytics');
    Route::get('/admin/bahan', ManageMaterials::class)->name('admin.materials');
    Route::redirect('/admin/materials', '/admin/bahan');
    Route::get('/admin/pesanan', ManageOrders::class)->name('admin.orders');
    Route::redirect('/admin/orders', '/admin/pesanan');
    Route::redirect('/admin', '/admin/dashboard');
    Route::redirect('/dashboard', '/admin/dashboard');

    // 3. Logout Global
    Route::post('/keluar', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/masuk');
    })->name('logout');
});
