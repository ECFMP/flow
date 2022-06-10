<?php

namespace App\Http\Controllers\Auth;

use App\Enums\RoleKey;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class VatsimConnectController
{
    public function callback()
    {
        /** @var \SocialiteProviders\Manager\OAuth2\User $vatsimUser */
        //$vatsimUser = Socialite::driver('vatsimconnect')->user();

        $user = User::firstWhere('id', 1203533);
        if (!$user) {
            $user = new User([
                'id' => 1203533,
                'role_id' => Role::firstWhere('key', RoleKey::SYSTEM)->id
            ]);
        }

        $user->name = 'a';
        $user->token = 'b';
        $user->refresh_token = 'c';
        $user->refresh_token_expires_at = now()->addSeconds(9999);
        $user->save();

        Auth::login($user);

        return to_route('filament.pages.dashboard');
    }
}
