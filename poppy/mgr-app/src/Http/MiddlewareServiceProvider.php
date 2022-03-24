<?php

namespace Poppy\MgrApp\Http;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class MiddlewareServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        /* MgrPage Permission
         * ---------------------------------------- */
        $router->aliasMiddleware('mgr-rbac', Middlewares\RbacPermission::class);

        $router->middlewareGroup('mgr-auth', [
            'api',
            'sys-auth:jwt_backend',
            'sys-jwt',
            'sys-disabled_pam',
            'sys-ban:backend',
            'mgr-rbac',
        ]);
    }
}