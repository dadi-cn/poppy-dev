<?php

use Illuminate\Routing\Router;

Route::group([
    'namespace' => 'Poppy\Version\Http\Request\Backend',
], function (Router $router) {
    $router->any('version', 'VersionController@index')
        ->name('py-version:backend.version.index');
    $router->any('version/establish/{id?}', 'VersionController@establish')
        ->name('py-version:backend.version.establish');
    $router->any('version/delete/{id}', 'VersionController@delete')
        ->name('py-version:backend.version.delete');
});