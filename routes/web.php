<?php

use App\Http\Controllers\Auth\VatsimConnectController;
use App\Http\Controllers\DocumentationController;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Models\User;
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

Route::middleware([RedirectIfAuthenticated::class])->get('/', function () {
    return to_route('filament.auth.login');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/auth/redirect', function () {
        $user = User::updateOrCreate(
            ['id' => 1203533],
            [
                'role_id' => 1,
                'name' => 'Test User',
            ]
        );
        Auth::login($user);
        return to_route('filament.pages.dashboard');
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
