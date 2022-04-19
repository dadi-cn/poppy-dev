<?php

use Illuminate\Routing\Router;

Route::group([
    'middleware' => 'mgr-auth',
    'namespace'  => 'Poppy\MgrApp\Http\Request\ApiMgrApp',
], function (Router $router) {
    // 用户信息
    $router->any('user/info', 'UserController@info')
        ->name('py-mgr-app:api.user.info');
    $router->any('user/password', 'UserController@password')
        ->name('py-mgr-app:api.user.password');
    $router->any('home/setting/{key}', 'HomeController@setting')
        ->name('py-mgr-app:api.home.setting');
    $router->any('home/clear-cache', 'HomeController@clearCache')
        ->name('py-mgr-app:api.home.clear_cache');
    $router->any('mail/test', 'MailController@test')
        ->name('py-mgr-app:api.mail.test');
    $router->any('mail/store', 'MailController@store')
        ->name('py-mgr-app:api.mail.store');


    $router->any('role', 'RoleController@index')
        ->name('py-mgr-app:api.role.index');
    $router->any('role/establish/{id?}', 'RoleController@establish')
        ->name('py-mgr-app:api.role.establish');
    $router->any('role/delete/{id?}', 'RoleController@delete')
        ->name('py-mgr-app:api.role.delete');
    $router->any('role/menu/{id}', 'RoleController@menu')
        ->name('py-mgr-app:api.role.menu');

    $router->any('pam', 'PamController@index')
        ->name('py-mgr-app:api.pam.index');
    $router->any('pam/establish/{id?}', 'PamController@establish')
        ->name('py-mgr-app:api.pam.establish');
    $router->any('pam/password/{id}', 'PamController@password')
        ->name('py-mgr-app:api.pam.password');
    $router->any('pam/disable/{id}', 'PamController@disable')
        ->name('py-mgr-app:api.pam.disable');
    $router->any('pam/enable/{id}', 'PamController@enable')
        ->name('py-mgr-app:api.pam.enable');
    $router->any('pam/log', 'PamController@log')
        ->name('py-mgr-app:api.pam.log');
    $router->any('pam/token', 'PamController@token')
        ->name('py-mgr-app:api.pam.token');
    $router->any('pam/ban/{id}/{type}', 'PamController@ban')
        ->name('py-mgr-app:api.pam.ban');
    $router->any('pam/delete_token/{id}', 'PamController@deleteToken')
        ->name('py-mgr-app:api.pam.delete_token');

    $router->any('ban', 'BanController@index')
        ->name('py-mgr-app:api.ban.index');
    $router->any('ban/establish/{id?}', 'BanController@establish')
        ->name('py-mgr-app:api.ban.establish');
    $router->any('ban/status', 'BanController@status')
        ->name('py-mgr-app:api.ban.status');
    $router->any('ban/type', 'BanController@type')
        ->name('py-mgr-app:api.ban.type');
    $router->any('ban/delete/{id}', 'BanController@delete')
        ->name('py-mgr-app:api.ban.delete');
});