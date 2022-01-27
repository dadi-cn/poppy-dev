<?php

namespace Poppy\MgrApp\Http;

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
            'prefix'     => 'api/backend/mgr-app',
            'middleware' => 'mgr-app-backend-auth',
        ], function () {
            require_once __DIR__ . '/Routes/api-backend.php';
        });
    }
}