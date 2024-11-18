<?php

namespace Modules\Acl\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TokenValidationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\jsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $tokenString = $request->header('Authorization');
        [$id, $token] = explode('|', $tokenString, 2);
        $id = trim(str_replace('Bearer', '', $id));
        $user = $request->user();
        $userToken = $user->currentAccessToken()->first();

        if ($userToken->id != $id || $user->id != $userToken->tokenable_id || !hash_equals($userToken->token, hash('sha256', $token))) {
            return response()->json(['success' => false, 'code' => 400, 'message' => 'Token is invalid'], 400);
        }

        $now = Carbon::now();
        if ($userToken->expires_at->diffInDays($now) != 0 || $now->gt($userToken->expires_at)) {
            return response()->json(['success' => false, 'code' => 401, 'message' => 'Token is Expired'], 401);
        }

        return $next($request);
    }
}
