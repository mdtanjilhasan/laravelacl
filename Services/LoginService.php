<?php

namespace Modules\Acl\Services;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Modules\Acl\Services\Contracts\Authenticable;

class LoginService implements Authenticable
{
    private string $email;

    public function login(array $credentials): array
    {
        $this->email = $credentials['email'];
        $this->ensureIsNotRateLimited();

        if ( ! request()->is('api/*') && ! Auth::attempt($credentials) ) {
            RateLimiter::hit($this->throttleKey());
            throw new InvalidArgumentException('Unable to login. Incorrect password.', 200);
        } else {
            $data = $this->authorizationByToken($credentials);
        }

        RateLimiter::clear($this->throttleKey());

        if ( ! request()->is('api/*') ) {
            request()->session()->regenerate();
            $user = Auth::user();
            return ['full_name' => $user->name, 'first_name' => $user->profile?->first_name, 'last_name' => $user->profile?->last_name, 'image' => $user->profile?->image_path];
        }

        return $data;
    }

    public function logout()
    {
        if (request()->is('api/*') && request()->user()->tokens()->exists()) {
            request()->user()->currentAccessToken()->delete();
        } else {
            Auth::guard('web')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }
    }

    private function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout( request() ) );
        $seconds = RateLimiter::availableIn($this->throttleKey());
        throw new InvalidArgumentException("Too many requests. Blocked for $seconds seconds.", 429);
    }

    private function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.$_SERVER['REMOTE_ADDR']);
    }

    private function authorizationByToken($credentials): array
    {
        $user = User::select('id', 'email', 'password')->with('profile:id,user_id,first_name,last_name')->where('email', $this->email)->first();

        if (!Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($this->throttleKey());
            throw new InvalidArgumentException('Unable to login. Incorrect password.', 200);
        }

        if (array_key_exists('device_id', $credentials) && $user->tokens()->exists()) {
            $user->tokens()->where('name', $credentials['device_id'])->delete();
        }

        preg_match('/(\d{5})(\d{5})/', time(), $matches);
        $tokenName = "$matches[1]$user->id$matches[2]";
        $token = $user->createToken($tokenName, ['*'], Carbon::now()->addDay());
        return ['token' => $token->plainTextToken, 'device_id' => $tokenName, 'user' => ['full_name' => $user->profile?->full_name, 'first_name' => $user->profile?->first_name, 'last_name' => $user->profile?->last_name, 'image' => $user->profile?->image_path]];
    }
}
