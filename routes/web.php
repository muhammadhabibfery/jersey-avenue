<?php

use App\Http\Livewire\Home;
use App\Http\Livewire\Jersey;
use App\Http\Livewire\League;
use App\Http\Livewire\JerseyDetail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', Home::class)
    ->name('home');
Route::get('/leagues', League::class)
    ->name('league');
Route::get('/jerseys', Jersey::class)
    ->name('jersey');
Route::get('/jersey/{jersey}', JerseyDetail::class)
    ->name('jersey.detail');

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')
        ->middleware('verified')
        ->name('dashboard');

    Route::view('/profile', 'profile.edit')
        ->name('profile.edit');
});

require __DIR__ . '/auth.php';
