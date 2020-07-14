<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        $resp['message'] = config('exceptions.unauthorized.message');
        $resp['status_code'] = config('exceptions.unauthorized.status_code');
        return $request->is('api/*') || $request->ajax() || $request->wantsJson() || $request->expectsJson()
                    ? response()->json($resp, config('exceptions.unauthorized.status_code'))
                    : route('login');
    }
}
