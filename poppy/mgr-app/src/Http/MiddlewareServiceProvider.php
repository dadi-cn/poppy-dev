<?php

namespace Poppy\MgrApp\Http;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class MiddlewareServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        // 管理中间件
        $router->middlewareGroup('mgr-auth', [
            'api',                   // Api
            'sys-auth:jwt_backend',  // Auth
            'sys-jwt',               // Pwd Changed
            'sys-ban:backend',       // Ban Backend
            'sys-disabled_pam',      // Pam Disabled
            'sys-mgr-rbac',          // Permission
        ]);

        // 开发中间件
        $router->middlewareGroup('dev-auth', [
            'api',                   // Api
            'sys-auth:jwt_develop',  // Auth
            'sys-jwt',               // Pwd Changed
            'sys-ban:develop',       // Ban Backend
            'sys-disabled_pam',      // Pam Disabled
            'sys-mgr-rbac',          // Permission
        ]);
    }
}