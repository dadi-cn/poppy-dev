<?php

namespace Poppy\System\Http;

use Illuminate\Contracts\Http\Kernel as KernelContract;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Poppy\System\Http\Middlewares\CrossRequest;

class MiddlewareServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        /* Single
         * ---------------------------------------- */
        $router->aliasMiddleware('sys-auth', Middlewares\Authenticate::class);
        $router->aliasMiddleware('sys-ban', Middlewares\Ban::class);
        $router->aliasMiddleware('sys-sso', Middlewares\Sso::class);
        $router->aliasMiddleware('sys-jwt', Middlewares\JwtAuthenticate::class);
        $router->aliasMiddleware('sys-auth_session', Middlewares\AuthenticateSession::class);
        $router->aliasMiddleware('sys-mgr-rbac', Middlewares\MgrRbacPermission::class);
        $router->aliasMiddleware('sys-disabled_pam', Middlewares\DisabledPam::class);
        $router->aliasMiddleware('sys-site_open', Middlewares\SiteOpen::class);
        $router->aliasMiddleware('sys-app_sign', Middlewares\AppSign::class);

        /*
        |--------------------------------------------------------------------------
        | Web Middleware
        |--------------------------------------------------------------------------
        |
        */
        /* 基于系统开关的权限验证
         * ---------------------------------------- */
        $router->middlewareGroup('web-base', [
            'web',
            'sys-site_open',
        ]);

        /* 独立的 web-auth 验证
         * ---------------------------------------- */
        $router->middlewareGroup('web-auth', [
            'web-base',
            'sys-auth:web',
            'sys-auth_session',
            'sys-disabled_pam',
        ]);

        /* Web + Auth 进行验证
         * ---------------------------------------- */
        $router->middlewareGroup('web-with-auth', [
            'sys-auth:web',
            'sys-auth_session',
            'sys-disabled_pam',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Api Middleware
        |--------------------------------------------------------------------------
        */

        $router->middlewareGroup('api-sign', [
            'sys-ban',          // 系统禁用
            'sys-app_sign',     // 签名
            'sys-site_open',    // 站点开启
        ]);

        $router->middlewareGroup('api-sso', [
            'sys-app_sign',     // 签名
            'sys-site_open',    // 网站开启
            'sys-ban:user',     // 禁用
            'sys-sso',          // 单点登录
            'sys-auth:jwt_web', // 用户登录
            'sys-disabled_pam', // 用户禁用
        ]);


        // cors for api
        $this->app->make(KernelContract::class)->prependMiddleware(CrossRequest::class);
    }
}