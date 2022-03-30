<?php

use Illuminate\Routing\Router;

Route::group([
    'namespace' => 'Poppy\Sms\Http\Request\ApiMgrApp',
], function (Router $router) {
    $router->any('sms', 'SmsController@index')
        ->name('py-sms:api-backend.sms.index');
    $router->any('sms/establish/{id?}', 'SmsController@establish')
        ->name('py-sms:api-backend.sms.establish');
    $router->any('sms/delete/{id?}', 'SmsController@delete')
        ->name('py-sms:api-backend.sms.delete');
});