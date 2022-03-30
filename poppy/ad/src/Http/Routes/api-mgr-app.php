<?php

use Illuminate\Routing\Router;

Route::group([
    'namespace' => 'Poppy\Ad\Http\Request\ApiMgrApp',
], function (Router $router) {
    /* 广告位管理
     * ---------------------------------------- */
    $router->any('place', 'PlaceController@index')
        ->name('py-ad:mgr_app.place.index');
    $router->any('place/establish/{id?}', 'PlaceController@establish')
        ->name('py-ad:mgr_app.place.establish');
    $router->any('place/delete/{id}', 'PlaceController@delete')
        ->name('py-ad:mgr_app.place.delete');

    /* 广告内容管理
     * ---------------------------------------- */
    $router->any('content', 'ContentController@index')
        ->name('py-ad:mgr_app.content.index');
    $router->any('content/establish/{id?}', 'ContentController@establish')
        ->name('py-ad:mgr_app.content.establish');
    $router->any('content/delete/{id}', 'ContentController@delete')
        ->name('py-ad:mgr_app.content.delete');
    $router->any('content/toggle/{id}', 'ContentController@toggle')
        ->name('py-ad:mgr_app.content.toggle');
});