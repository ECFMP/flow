<?php

use App\Http\Controllers\Auth\VatsimConnectController;
use App\Http\Controllers\DocumentationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['guest'])->group(function () {
    Route::get('admin/login', function () {
        // Just redirect to login, we don't work with passwords
        return to_route('vatsimconnect.redirect');
    })->name('filament.auth.login');

    Route::get('/auth/redirect', function () {
        return Socialite::driver('vatsimconnect')->redirect();
    })->name('vatsimconnect.redirect');

    Route::get('/auth/callback', [VatsimConnectController::class, 'callback']);
});

Route::controller(DocumentationController::class)
    ->prefix('docs')
    ->group(function () {
        Route::get('v{number}', 'documentationView')
            ->where(['number' => '\d+']);
        });
