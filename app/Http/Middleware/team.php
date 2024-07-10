<?php

namespace App\Http\Middleware;

use Closure;

class team
{
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        // $user = \App\Models\User::where('api_token', $token)->first();
        // dd(auth());
        // dd(auth());
        $user = auth()->user()->teams()->pluck('team_id');
        setPermissionsTeamId($user);

        return $next($request);
        // dd($user);
        if ($user) {
            auth()->login($user);
            return $next($request);
        }
        return response([
            'message' => 'Unauthenticated'
        ], 403);
    }
}