<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // We are providing in this example a simple header token authentication
        // This can be set with the var ACCESS_TOKEN in the .env file
        if ($request->header('auth_token') !== config('app.auth_token')) {
            return response('Unauthorized. - Incorrect token', Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
