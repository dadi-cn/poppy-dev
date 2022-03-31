<?php

use Illuminate\Routing\Router;

Route::group([
    'middleware' => 'dev-auth',
    'namespace'  => 'Poppy\MgrApp\Http\Request\ApiDev',
], function (Router $router) {
    $router->any('apidoc/json', 'ApidocController@json')
        ->name('py-mgr-dev:api.apidoc.json');
});
