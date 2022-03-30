<?php

namespace Poppy\AliyunOss\Http;

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
        $this->mapApiRoutes();
        $this->mapApiMgrAppRoutes();
    }

    /**
     * Define the "web" routes for the module.
     * These routes all receive session state, CSRF protection, etc.
     * @return void
     */
    private function mapBackendRoutes(): void
    {
        Route::group([
            'prefix'     => $this->prefix . '/aliyun-oss',
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
    private function mapApiRoutes()
    {
        Route::group([
            'middleware' => 'api-sign',
            'prefix'     => 'api_v1/aliyun-oss',
        ], function () {
            require_once __DIR__ . '/Routes/api_v1.php';
        });
    }


    /**
     * Define the "web" routes for the module.
     * These routes all receive session state, CSRF protection, etc.
     * @return void
     */
    private function mapApiMgrAppRoutes(): void
    {
        Route::group([
            'prefix'     => 'api/mgr-app/py-aliyun-oss',
            'middleware' => 'mgr-auth',
        ], function () {
            require_once __DIR__ . '/Routes/api-mgr-app.php';
        });
    }
}