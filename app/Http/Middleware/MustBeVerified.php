<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class MustBeVerified
{

    /**
     * The message that unverified users will see
     */
    const EXCEPTION_MESSAGE = "You do not have permission to view this page or perform this action.";

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return auth()->user()->hasNotVerifiedEmail()
        ? throw new AuthorizationException(static::EXCEPTION_MESSAGE)
        : $next($request);
    }
}