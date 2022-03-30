<?php

namespace Poppy\Sms\Http;

use Route;

class RouteServiceProvider extends \Poppy\Framework\Application\RouteServiceProvider
{
    /**
     * Define the routes for the module.
     * @return void
     */
    public function map(): void
    {
        $this->mapBackendRoutes();
    }

    /**
     * Define the "web" routes for the module.
     * These routes all receive session state, CSRF protection, etc.
     * @return void
     */
    protected function mapBackendRoutes(): void
    {
        Route::group([
            'prefix'     => $this->prefix . '/py-sms',
            'middleware' => 'backend-auth',
        ], function () {
            require_once __DIR__ . '/Routes/backend.php';
        });


        Route::group([
            'prefix'     => 'api/mgr-app/py-sms',
            'middleware' => 'mgr-auth',
        ], function () {
            require_once __DIR__ . '/Routes/api-mgr-app.php';
        });
    }
}