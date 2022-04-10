<?php

namespace Demo\Http;

use Illuminate\Routing\Router;
use Route;

class RouteServiceProvider extends \Poppy\Framework\Application\RouteServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     * In addition, it is set as the URL generator's root namespace.
     * @var string
     */
    protected $namespace = 'Demo\Http\Request';

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
    }

    /**
     * Define the "web" routes for the module.
     * These routes all receive session state, CSRF protection, etc.
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'prefix' => 'demo',
        ], function (Router $route) {
            require_once poppy_path('demo', 'src/http/routes/web.php');
        });

        Route::group([], function (Router $route) {
            require_once poppy_path('demo', 'src/http/routes/web-root.php');
        });

        Route::group([
            'prefix'     => $this->prefix . '/demo',
            'middleware' => 'backend-auth',
        ], function (Router $route) {
            require_once poppy_path('demo', 'src/http/routes/backend.php');
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
            'prefix' => 'api/demo',
        ], function (Router $route) {
            require_once poppy_path('demo', 'src/http/routes/api.php');
        });
    }
}
