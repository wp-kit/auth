<?php

namespace WPKit\Auth\Middleware;

use Closure;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;

class BasicAuth extends AuthenticateWithBasicAuth
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        return $this->auth->guard($guard)->basic('user_login') ?: $next($request);
    }
}
