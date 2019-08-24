<?php
/*
|--------------------------------------------------------------------------
| Demo
|--------------------------------------------------------------------------
|
*/
Route::group([
	'middleware' => ['cross'],
	'namespace'  => 'DummyNamespace\Http\Request\Web',
], function (Illuminate\Routing\Router $route) {
	$route->get('/', 'DemoController@index');
});