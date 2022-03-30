<?php

namespace Poppy\MgrApp\Http;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class MiddlewareServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        $router->middlewareGroup('mgr-login', [
            'api',
            'sys-ban:backend', // 系统禁用
            // todo Add Sign
        ]);

        $router->middlewareGroup('mgr-auth', [
            'api',                   // Api
            'sys-auth:jwt_backend',  // Auth
            'sys-jwt',               // Pwd Changed
            'sys-ban:backend',       // Ban Backend
            'sys-disabled_pam',      // Pam Disabled
            'sys-mgr-rbac',          // Permission
        ]);
    }
}