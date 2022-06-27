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
    $route->get('resp/validator', 'RespController@validator');
    $route->get('resp/401', 'RespController@unAuth');
    $route->get('resp/header', 'RespController@header');
    // form 数据
    $route->any('form/{field}', 'MgrAppController@form')
        ->name('demo:api.mgr_app.form');
    $route->any('grid-npk/{grid}', 'MgrAppController@gridNoPk')
        ->name('demo:api.mgr_app.grid-npk');

    // table
    $route->any('table/{table}', 'TableController@index')
        ->name('demo:api.table.index');

    // grid
    $route->any('filter/{filter}', 'MgrAppController@filter')
        ->name('demo:api.mgr_app.filter');

    // table
    $route->any('grid/auto/{type}', 'GridController@auto')
        ->name('demo:api.grid.auto');
    $route->any('grid/request/{type}', 'GridController@request')
        ->name('demo:api.grid.request');

    // form
    $route->any('form/auto/{type}', 'FormController@auto')
        ->name('demo:api.form.auto');


    $route->any('grid-plugin', 'MgrAppController@gridPlugin')
        ->name('demo:api.mgr_app.grid_plugin');
    $route->any('grid-model', 'MgrAppController@gridModel')
        ->name('demo:api.mgr_app.grid_model');
    $route->any('grid-view', 'MgrAppController@gridView')
        ->name('demo:api.mgr_app.grid_view');

    // 单页面配置
    $route->any('dashboard/{type}', 'MgrAppController@dashboard')
        ->name('demo:api.mgr_app.dashboard');
});

Route::group([
    'middleware' => ['api-sso'],
    'namespace'  => 'Demo\Http\Request\Api\Web',
], function (Illuminate\Routing\Router $route) {
    $route->post('sso/access', 'SsoController@access');
});