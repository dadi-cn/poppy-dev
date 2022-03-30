<?php

use Illuminate\Routing\Router;

Route::group([
    'namespace' => 'Poppy\SensitiveWord\Http\Request\ApiMgrApp',
], function (Router $router) {
    $router->any('/', 'WordController@index')
        ->name('py-sensitive-word:api-backend.word.index');
    $router->any('establish/{id?}', 'WordController@establish')
        ->name('py-sensitive-word:api-backend.word.establish');
    $router->any('delete/{id?}', 'WordController@delete')
        ->name('py-sensitive-word:api-backend.word.delete');
});