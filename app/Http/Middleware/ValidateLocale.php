<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateLocale
{
    /**
     * Set the application locale based on the user's preferred locale from settings
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            app()->setLocale($request->user()->settings['locale']);
        }

        return $next($request);
    }
}
