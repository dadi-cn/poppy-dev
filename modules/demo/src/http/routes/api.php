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

    $route->any('grid-npk/{grid}', 'MgrAppController@gridNoPk')
        ->name('demo:api.mgr_app.grid-npk');

    // table
    $route->any('table/ez', 'TableController@ez')
        ->name('demo:api.table.ez');

    // grid
    $route->any('filter/{filter}', 'MgrAppController@filter')
        ->name('demo:api.mgr_app.filter');

    // table
    $route->any('grid/auto/{type}', 'GridController@auto')
        ->name('demo:api.grid.auto');
    $route->any('grid/request/{type}', 'GridController@request')
        ->name('demo:api.grid.request');
    $route->any('grid/custom_query', 'GridController@customQuery')
        ->name('demo:api.grid.custom_query');
    $route->any('grid/ctrl', 'GridController@ctrl')
        ->name('demo:api.grid.ctrl');
    $route->any('grid/motion', 'GridController@motion')
        ->name('demo:api.grid.motion');

    // form
    $route->any('form/auto/{type}', 'FormController@auto')
        ->name('demo:api.form.auto');
    $route->any('form/cascader', 'FormController@cascader')
        ->name('demo:api.form.cascader');
    $route->any('form/ctrl', 'FormController@ctrl')
        ->name('demo:api.form.ctrl');


    $route->any('grid-plugin', 'MgrAppController@gridPlugin')
        ->name('demo:api.mgr_app.grid_plugin');
    $route->any('grid-model', 'MgrAppController@gridModel')
        ->name('demo:api.mgr_app.grid_model');
    $route->any('grid-view', 'MgrAppController@gridView')
        ->name('demo:api.mgr_app.grid_view');

    // ???????????????
    $route->any('dashboard/{type}', 'MgrAppController@dashboard')
        ->name('demo:api.mgr_app.dashboard');
});

Route::group([
    'middleware' => ['api-sso'],
    'namespace'  => 'Demo\Http\Request\Api\Web',
], function (Illuminate\Routing\Router $route) {
    $route->post('sso/access', 'SsoController@access');
});