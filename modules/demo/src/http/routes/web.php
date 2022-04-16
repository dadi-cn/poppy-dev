<?php

use Illuminate\Routing\Router;

Route::group([
    'namespace' => 'Demo\Http\Request\Web',
], function (Router $router) {
    // 所有 Demo 界面
    $router->any('demo', 'DemoController@index')
        ->name('demo:web.demo.index');

    $router->any('content', 'ContentController@index')
        ->name('demo:web.content.index');
    $router->any('content/form', 'ContentController@form')
        ->name('demo:web.content.form');
    $router->any('form/{type}', 'FormController@index')
        ->name('demo:web.form.index');
    $router->any('table', 'TableController@index')
        ->name('demo:web.table.index');
    $router->any('table/demo/{type?}', 'TableController@demo')
        ->name('demo:web.table.grid_demo');

    // EnvHelper
    $router->any('helper/env', 'HelperController@env')
        ->name('demo:web.helper.env');
    $router->any('helper/img_str', 'HelperController@imgStr')
        ->name('demo:web.helper.img_str');
    $router->any('helper/img_bmp', 'HelperController@imgBmp')
        ->name('demo:web.helper.img_bmp');
    $router->any('helper/image', 'HelperController@image')
        ->name('demo:web.helper.image');
    $router->any('helper/tree', 'HelperController@tree')
        ->name('demo:web.helper.tree');
    // env-helper
    $router->any('env/{type}', 'EnvHelperController@index')
        ->name('demo:web.env.index');


    $router->any('token', 'TokenController@index')
        ->name('demo:web.token.index');

    $router->any('header', 'HeaderController@index')
        ->name('demo:web.header.index');

    /* Layout
     * ---------------------------------------- */
    $router->any('layout/fe', 'JsController@fe')
        ->name('demo:web.js.fe');
    $router->any('l/{page?}', 'JsController@index')
        ->name('demo:web.js.index');
    $router->any('mail/{slug}/{page?}', 'JsController@mail')
        ->name('demo:web.js.mail');
});
