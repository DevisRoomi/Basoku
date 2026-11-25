<?php

use App\Livewire\TrackOrder;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('/sejarah-bakso', 'bakso-history')->name('bakso.history');
Route::view('/kalkulator', 'calculator-page')->name('calculator');
Route::view('/daftar-harga', 'catalog-page')->name('catalog');
Route::get('/lacak-pesanan', TrackOrder::class)->name('orders.track');

// Rute Terpisah (Auth, Customer, Admin)
require __DIR__.'/auth.php';
require __DIR__.'/user.php';
require __DIR__.'/admin.php';
