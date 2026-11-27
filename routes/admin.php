<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ManageKioskSettings;
use App\Livewire\Admin\ManageMaterials;
use App\Livewire\Admin\ManageOrders;
use App\Livewire\Admin\ManageRecipes;
use Illuminate\Support\Facades\Route;

// Panel Khusus Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/analytics', Dashboard::class)->name('analytics');
    Route::get('/bahan', ManageMaterials::class)->name('materials');
    Route::get('/resep', ManageRecipes::class)->name('recipes');
    Route::get('/pesanan', ManageOrders::class)->name('orders');
    Route::get('/pengaturan-kios', ManageKioskSettings::class)->name('kiosk');
});

// Alias & Redirect Global Admin (tanpa prefix nama agar tidak bentrok pada route:cache)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::redirect('/admin', '/admin/dashboard');
    Route::redirect('/dashboard', '/admin/dashboard');
    Route::redirect('/admin/materials', '/admin/bahan');
    Route::redirect('/admin/recipes', '/admin/resep');
    Route::redirect('/admin/orders', '/admin/pesanan');
});
