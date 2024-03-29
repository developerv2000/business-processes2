<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Abort with a 404 response if action is not allowed for user
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$allowedRoles): Response
    {
        // No restrictions for admins
        if ($request->user()->isAdmin()) return $next($request);

        $userRoles = $request->user()->roles;

        // Check if any of the user's roles match the allowed roles
        $allowed = $userRoles->contains(function ($role) use ($allowedRoles) {
            return in_array($role->name, $allowedRoles);
        });

        if (!$allowed) abort(404);

        return $next($request);
    }
}
