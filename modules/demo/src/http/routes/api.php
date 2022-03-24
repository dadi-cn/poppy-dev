<?php
/*
|--------------------------------------------------------------------------
| Demo
|--------------------------------------------------------------------------
|
*/
Route::group([
    'middleware' => ['cross'],
    'namespace'  => 'Demo\Http\Request\Api\Web',
], function (Illuminate\Routing\Router $route) {
    $route->get('apidoc/how', 'ApiDocController@how');
    $route->get('resp/success', 'RespController@success');
    $route->get('resp/error', 'RespController@error');
    $route->get('resp/401', 'RespController@unAuth');
    $route->get('resp/header', 'RespController@header');
    // form 数据
    $route->any('form/{field}', 'MgrAppController@form')
        ->name('demo:api.mgr_app.form');
    $route->any('grid/{grid}', 'MgrAppController@grid')
        ->name('demo:api.mgr_app.grid');
    $route->any('filter/{filter}', 'MgrAppController@filter')
        ->name('demo:api.mgr_app.filter');
    $route->any('table/{table}', 'TableController@index')
        ->name('demo:api.table.index');
    $route->any('grid_request/{type}', 'MgrAppController@gridRequest')
        ->name('demo:api.mgr_app.grid_request');
    $route->any('grid_form/{type}', 'MgrAppController@gridForm')
        ->name('demo:api.mgr_app.grid_form');
});

Route::group([
    'middleware' => ['api-sso'],
    'namespace'  => 'Demo\Http\Request\Api\Web',
], function (Illuminate\Routing\Router $route) {
    $route->post('sso/access', 'SsoController@access');
});