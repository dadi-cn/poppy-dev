<?php

namespace Poppy\Area\Http;


use Route;

class RouteServiceProvider extends \Poppy\Framework\Application\RouteServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     * In addition, it is set as the URL generator's root namespace.
     * @var string
     */
    protected $namespace = 'Poppy\Area\Http\Request';

    /**
     * Define your route model bindings, pattern filters, etc.
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the module.
     * @return void
     */
    public function map()
    {
        $this->mapWebRoutes();

        $this->mapApiRoutes();

        $this->mapBackendRoutes();
    }

    /**
     * Define the "web" routes for the module.
     * These routes all receive session state, CSRF protection, etc.
     * @return void
     */
    protected function mapWebRoutes()
    {
        // backend web
        Route::group([
            'prefix'     => $this->prefix . '/area',
            'middleware' => 'backend-auth',
        ], function () {
            require_once __DIR__ . '/Routes/backend.php';
        });
    }

    /**
     * Define the "api" routes for the module.
     * These routes are typically stateless.
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => 'api-sign',
            'prefix'     => 'api_v1/area',
        ], function () {
            require_once __DIR__ . '/Routes/api_v1.php';
        });
    }

    /**
     * Define the "web" routes for the module.
     * These routes all receive session state, CSRF protection, etc.
     * @return void
     */
    protected function mapBackendRoutes(): void
    {
        Route::group([
            'prefix'     => 'api/mgr-app/py-area',
            'middleware' => 'mgr-auth',
        ], function () {
            require_once __DIR__ . '/Routes/api-mgr-app.php';
        });
    }
}
