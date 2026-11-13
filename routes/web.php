<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ManageMaterials;
use App\Livewire\Admin\ManageOrders;
use App\Livewire\Admin\ManageRecipes;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Customer\MyOrders;
use App\Livewire\Customer\MyRecipeLab;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('/sejarah-bakso', 'bakso-history')->name('bakso.history');
Route::view('/kalkulator', 'calculator-page')->name('calculator');
Route::view('/daftar-harga', 'catalog-page')->name('catalog');

// Rute Terpisah (Auth, Customer, Admin)
require __DIR__ . '/auth.php';
require __DIR__ . '/user.php';
require __DIR__ . '/admin.php';
