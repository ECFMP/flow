<?php

namespace App\Http\Controllers\Auth;

use App\Enums\RoleKey;
use App\Models\Role;
use App\Models\User;
use Exception;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class VatsimConnectController
{
    public function callback()
    {
        try {
            /** @var \SocialiteProviders\Manager\OAuth2\User $vatsimUser */
            $vatsimUser = Socialite::driver('vatsimconnect')->user();

            $user = User::firstWhere('id', $vatsimUser->id);
            if (!$user) {
                $user = new User([
                    'id' => $vatsimUser->getId(),
                    'role_id' => Role::firstWhere('key', RoleKey::USER)->id
                ]);
            }

            $user->name = $vatsimUser->name;
            $user->token = $vatsimUser->token;
            $user->refresh_token = $vatsimUser->refreshToken;
            $user->refresh_token_expires_at = now()->addSeconds($vatsimUser->expiresIn);
            $user->saveQuietly();

            Auth::login($user);

            return to_route('filament.pages.dashboard');
        } catch (Exception $e) {
            Log::error('Exception on login: ' . $e->getMessage());
            Filament::notify('danger', __('Something went wrong, please try again'));
            return to_route('filament.auth.login');
        }
    }
}
