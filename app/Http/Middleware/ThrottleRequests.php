<?php

namespace App\Http\Middleware;

use Closure;

class ThrottleRequests extends \Illuminate\Routing\Middleware\ThrottleRequests
{
    /**
     * 路由白名单（前后去掉'/'）
     * @var array
     */
    protected $pathWhiteList = [
        'collect/form_id'
    ];

    /**
     * ip白名单
     * @var array
     */
    protected $ipWhiteList = [
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        $key = $this->resolveRequestSignature($request);

        $maxAttempts = $this->resolveMaxAttempts($request, $maxAttempts);

        // 白名单路由和ip无访问次数限制
        if (in_array($request->ip(), $this->ipWhiteList) || in_array($request->path(), $this->pathWhiteList)) {
            return $next($request);
        }

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            throw $this->buildException($key, $maxAttempts);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addHeaders(
            $response, $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }
}
