<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ManageMaterials;
use App\Livewire\Admin\ManageOrders;
use App\Livewire\Admin\ManageRecipes;
use Illuminate\Support\Facades\Route;

// Panel Khusus Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/analytics', Dashboard::class)->name('analytics');
    Route::get('/bahan', ManageMaterials::class)->name('materials');
    Route::redirect('/materials', '/admin/bahan');
    Route::get('/resep', ManageRecipes::class)->name('recipes');
    Route::redirect('/recipes', '/admin/resep');
    Route::get('/pesanan', ManageOrders::class)->name('orders');
    Route::redirect('/orders', '/admin/pesanan');
});

// Alias & Redirect Global Admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::redirect('/admin', '/admin/dashboard');
    Route::redirect('/dashboard', '/admin/dashboard');
});
