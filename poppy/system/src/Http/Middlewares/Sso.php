<?php

namespace Poppy\System\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Poppy\Core\Redis\RdsDb;
use Poppy\System\Classes\PySystemDef;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

/**
 * 单点登录
 */
class Sso extends BaseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = jwt_token();

        try {
            if (!$token || !$payload = $this->auth->setToken($token)->check(true)) {
                return response('Unauthorized Jwt.', 401);
            }
            // 这里会抛出异常, IDE 提示不正确
        } catch (TokenInvalidException $e) {
            return response('Unauthorized Jwt. Sso check token invalid', 401);
        }


        // 是否开启单点登录
        if (!\Poppy\System\Action\Sso::isEnable()) {
            return $next($request);
        }

        // sso check
        $md5Token = md5($token);
        $pamId    = data_get($payload, 'user.id');
        $Rds      = RdsDb::instance();
        $hash     = $Rds->hGet(PySystemDef::ckTagSso('valid'), $pamId);
        if (Str::contains($hash, '|')) {
            $rdsHash = Str::before($hash, '|');
            if ($rdsHash === $md5Token) {
                return $next($request);
            }
            else {
                return response('Unauthorized Jwt, Token Expired.', 401);
            }
        }
        else {
            return response('Unauthorized Jwt, Token unValid.', 401);
        }
    }
}