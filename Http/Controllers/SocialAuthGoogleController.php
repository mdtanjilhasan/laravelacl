<?php

namespace Modules\Acl\Http\Controllers;

use Exception;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;

class SocialAuthGoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            $user = User::where('email', $user->getEmail())->first();

            if (! empty($user) ) {
                Auth::login($user);
                $url = '/my-account';
                return redirect()->to($url);
            } else {
                return redirect()->to('/?u=invalid');
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
