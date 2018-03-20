<?php
/*/*
|--------------------------------------------------------------------------
| Util
|--------------------------------------------------------------------------
|
*/
\Route::group([
	'middleware' => ['cross'],
], function (Illuminate\Routing\Router $route) {
	// 获取图像和地区代码
	$route->get('/', function(){
		return 'install success';
	});
});