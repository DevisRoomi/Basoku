<?php

use App\Livewire\Customer\MyOrders;
use App\Livewire\Customer\MyRecipeLab;
use Illuminate\Support\Facades\Route;

// Area Pelanggan Terproteksi
Route::middleware(['auth'])->group(function () {
    Route::get('/racikan-saya', MyRecipeLab::class)->name('customer.recipes');
    Route::redirect('/resep-saya', '/racikan-saya');
    Route::get('/pesanan-saya', MyOrders::class)->name('customer.orders');
    Route::redirect('/order-saya', '/pesanan-saya');
});
