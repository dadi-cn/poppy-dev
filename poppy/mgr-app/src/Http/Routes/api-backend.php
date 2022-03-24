<?php

use Illuminate\Routing\Router;

Route::group([
    'namespace' => 'Poppy\MgrApp\Http\Request\Api\Backend',
], function (Router $router) {
    // 用户信息
    $router->any('user/info', 'UserController@info')
        ->name('py-mgr-app:api-backend.user.info');
    $router->any('user/password', 'UserController@password')
        ->name('py-mgr-app:api-backend.user.password');
    $router->any('home/setting/{key}', 'HomeController@setting')
        ->name('py-mgr-app:api-backend.home.setting');
    $router->any('home/upload', 'HomeController@upload')
        ->name('py-mgr-app:api-backend.home.upload');
    $router->any('home/clear-cache', 'HomeController@clearCache')
        ->name('py-mgr-app:api-backend.home.clear_cache');
    $router->any('mail/test', 'MailController@test')
        ->name('py-mgr-app:api-backend.mail.test');
    $router->any('mail/store', 'MailController@store')
        ->name('py-mgr-app:api-backend.mail.store');


    $router->any('role', 'RoleController@index')
        ->name('py-mgr-app:api-backend.role.index');
    $router->any('role/establish/{id?}', 'RoleController@establish')
        ->name('py-mgr-app:api-backend.role.establish');
    $router->any('role/delete/{id?}', 'RoleController@delete')
        ->name('py-mgr-app:api-backend.role.delete');
    $router->any('role/menu/{id}', 'RoleController@menu')
        ->name('py-mgr-app:api-backend.role.menu');

    $router->any('pam', 'PamController@index')
        ->name('py-mgr-app:api-backend.pam.index');
    $router->any('pam/establish/{id?}', 'PamController@establish')
        ->name('py-mgr-app:api-backend.pam.establish');
    $router->any('pam/password/{id}', 'PamController@password')
        ->name('py-mgr-app:api-backend.pam.password');
    $router->any('pam/disable/{id}', 'PamController@disable')
        ->name('py-mgr-app:api-backend.pam.disable');
    $router->any('pam/enable/{id}', 'PamController@enable')
        ->name('py-mgr-app:api-backend.pam.enable');
    $router->any('pam/log', 'PamController@log')
        ->name('py-mgr-app:api-backend.pam.log');
    $router->any('pam/token', 'PamController@token')
        ->name('py-mgr-app:api-backend.pam.token');
    $router->any('pam/ban/{id}/{type}', 'PamController@ban')
        ->name('py-mgr-app:api-backend.pam.ban');
    $router->any('pam/delete_token/{id}', 'PamController@deleteToken')
        ->name('py-mgr-app:api-backend.pam.delete_token');

    $router->any('ban', 'BanController@index')
        ->name('py-mgr-app:api-backend.ban.index');
    $router->any('ban/establish/{id?}', 'BanController@establish')
        ->name('py-mgr-app:api-backend.ban.establish');
    $router->any('ban/status', 'BanController@status')
        ->name('py-mgr-app:api-backend.ban.status');
    $router->any('ban/type', 'BanController@type')
        ->name('py-mgr-app:api-backend.ban.type');
    $router->any('ban/delete/{id}', 'BanController@delete')
        ->name('py-mgr-app:api-backend.ban.delete');
});