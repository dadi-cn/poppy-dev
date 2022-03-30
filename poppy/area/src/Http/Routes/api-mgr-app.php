<?php

use Illuminate\Routing\Router;

Route::group([
    'namespace' => 'Poppy\Area\Http\Request\ApiMgrApp',
], function (Router $router) {
    $router->any('content', 'ContentController@index')
        ->name('py-area:api-backend.content.index');
    $router->any('content/establish/{id?}', 'ContentController@establish')
        ->name('py-area:api-backend.content.establish');
    $router->any('content/delete/{id?}', 'ContentController@delete')
        ->name('py-area:api-backend.content.delete');
    $router->any('content/fix', 'ContentController@fix')
        ->name('py-area:api-backend.content.fix');
});