<?php

namespace App\Http\Middleware;

use Closure;
use \Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class WeChatCurrentLoginUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function handle($request, Closure $next)
    {
        if (env('TEST_OPEN_ID') && env('TEST_USER_ID')) {
            $request->openId     = env('TEST_OPEN_ID');
            $request->currUserId = env('TEST_USER_ID');
            return $next($request);
        }

        $openId     = session('wechat.oauth_user.default')->getId();
        $currUserId = session('wechat_current_user_id', 0);
        if (empty($currUserId)) {
            $user = User::where('app_source', User::APP_WECHAT_QUXIANG)->where('openid', $openId)->first();
            if (!$user instanceof User) {
                abort(500, '请先登录');
            }
            $currUserId = $user->user_id;
            session(['wechat_current_user_id' => $currUserId]);
        }
        $request->openId     = $openId;
        $request->currUserId = $currUserId;
        return $next($request);
    }

}
