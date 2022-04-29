<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class VatsimConnectController extends Controller
{
    public function callback()
    {
        /** @var \SocialiteProviders\Manager\OAuth2\User $vatsimUser */
        $vatsimUser = Socialite::driver('vatsimconnect')->user();

        $user = User::firstWhere('id', $vatsimUser->id);
        if (!$user) {
            $user = new User([
                'id' => $vatsimUser->getId(),
                'role_id' => Role::firstWhere('key', 'USER')->value('id')
            ]);
        }

        $user->name = $vatsimUser->name;
        $user->token = $vatsimUser->token;
        $user->refresh_token = $vatsimUser->refreshToken;
        $user->refresh_token_expires_at = now()->addSeconds($vatsimUser->expiresIn);
        $user->save();

        // TODO: Return to /admin (or whatever url we choose) once Filament has been added
        return redirect('/');
    }
}
