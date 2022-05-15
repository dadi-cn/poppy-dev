<?php

namespace Poppy\MgrPage\Http;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class MiddlewareServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        $router->middlewareGroup('develop-auth', [
            'web',
            'sys-site_open',
            'sys-auth:develop',
            'sys-auth_session',
            'sys-disabled_pam',
            'sys-mgr-rbac',
        ]);

        $router->middlewareGroup('backend-auth', [
            'web',
            'sys-auth:backend',
            'sys-auth_session',
            'sys-disabled_pam',
            'sys-ban:backend',
            'sys-mgr-rbac',
        ]);
    }
}