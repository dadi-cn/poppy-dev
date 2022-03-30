<?php

namespace Poppy\Ad\Http;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     * In addition, it is set as the URL generator's root namespace.
     * @var string
     */
    protected $namespace = 'Poppy\Ad\Request';

    /**
     * Define the routes for the module.
     * @return void
     */
    public function map()
    {
        // backend web
        Route::group([
            'prefix'     => 'api/mgr-app/py-ad',
            'middleware' => 'mgr-auth',
        ], function () {
            require_once __DIR__ . '/Routes/api-mgr-app.php';
        });
    }
}
