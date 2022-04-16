<?php
/*
|--------------------------------------------------------------------------
| 系统路由
|--------------------------------------------------------------------------
|
*/

Route::group([
    'namespace'  => 'Poppy\System\Http\Request\ApiV1\Web',
], function (Illuminate\Routing\Router $route) {
    $route->post('core/doc', 'CoreController@doc');
});

/* 核心信息无需禁用, 仅需要加密鉴权即可
 * ---------------------------------------- */
Route::group([
    'middleware' => ['sys-app_sign'],
    'namespace'  => 'Poppy\System\Http\Request\ApiV1\Web',
], function (Illuminate\Routing\Router $route) {
    $route->post('core/info', 'CoreController@info');
    $route->post('core/translate', 'CoreController@translate');
    $route->post('core/mock', 'CoreController@mock');
});

/* 可以对用户设备进行封禁
 * ---------------------------------------- */
Route::group([
    'middleware' => ['api-sign'],
    'namespace'  => 'Poppy\System\Http\Request\ApiV1\Web',
], function (Illuminate\Routing\Router $route) {
    $route->post('auth/login', 'AuthController@login')
        ->name('py-system:pam.auth.login');

    // captcha
    $route->post('captcha/verify_code', 'CaptchaController@verifyCode');
    $route->post('captcha/send', 'CaptchaController@send');
    $route->post('captcha/fetch', 'CaptchaController@fetch');

    // auth
    $route->post('auth/reset_password', 'AuthController@resetPassword');
    $route->post('auth/bind_mobile', 'AuthController@bindMobile');
});

// Jwt 合法性验证
Route::group([
    'middleware' => ['sys-jwt'],
    'namespace'  => 'Poppy\System\Http\Request\ApiV1\Web',
], function (Illuminate\Routing\Router $route) {
    $route->post('upload/image', 'UploadController@image')
        ->name('py-system:api_v1.upload.image');
    $route->post('upload/file', 'UploadController@file')
        ->name('py-system:api_v1.upload.file');
});

// 单点登录
Route::group([
    'middleware' => ['api-sso'],
    'namespace'  => 'Poppy\System\Http\Request\ApiV1\Web',
], function (Illuminate\Routing\Router $route) {
    $route->post('auth/access', 'AuthController@access')
        ->name('py-system:pam.auth.access');
    $route->post('auth/renew', 'AuthController@renew')
        ->name('py-system:pam.auth.renew');
    $route->post('auth/logout', 'AuthController@logout')
        ->name('py-system:pam.auth.logout');
});
