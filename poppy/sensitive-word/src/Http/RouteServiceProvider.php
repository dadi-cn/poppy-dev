<?php

namespace Poppy\SensitiveWord\Http;

use Route;

class RouteServiceProvider extends \Poppy\Framework\Application\RouteServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     * In addition, it is set as the URL generator's root namespace.
     * @var string
     */
    protected $namespace = 'Poppy\SensitiveWord\Http\Request';

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
            'prefix'     => $this->prefix . '/sensitive-word',
            'middleware' => 'backend-auth',
        ], function () {
            require_once __DIR__ . '/Routes/backend.php';
        });

        Route::group([
            'prefix'     => 'api/mgr-app/py-sensitive-word',
            'middleware' => 'mgr-auth',
        ], function () {
            require_once __DIR__ . '/Routes/api-mgr-app.php';
        });
    }
}
