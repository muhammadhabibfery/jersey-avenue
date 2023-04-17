<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Home;
use App\Http\Livewire\Jersey;
use App\Http\Livewire\League;
use App\Http\Livewire\JerseyDetail;
use App\Http\Livewire\Cart;

Route::get('/', Home::class)
    ->name('home');
Route::get('/leagues', League::class)
    ->name('league');
Route::get('/jerseys', Jersey::class)
    ->name('jersey');
Route::get('/jersey/{jersey}', JerseyDetail::class)
    ->name('jersey.detail');

Route::middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/cart', Cart::class)
            ->name('cart');
    });
