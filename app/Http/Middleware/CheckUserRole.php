<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class CheckUserRole extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  array $roles
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
//        if (! $request->user()->hasRole($roles)) {
//            // TODO:: 没有权限
//        }
        return $next($request);
    }
}