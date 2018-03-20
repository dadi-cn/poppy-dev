<?php namespace DummyNamespace\Routes;

/**
 * Copyright (C) Update For IDE
 */
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
	/**
	 * This namespace is applied to your controller routes.
	 * In addition, it is set as the URL generator's root namespace.
	 * @var string
	 */
	protected $namespace = 'DummyNamespace\Request';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 * @return void
	 */
	public function boot()
	{
		parent::boot();
	}

	/**
	 * Define the routes for the module.
	 * @return void
	 */
	public function map()
	{
		$this->mapWebRoutes();

		$this->mapApiRoutes();
	}

	/**
	 * Define the "web" routes for the module.
	 * These routes all receive session state, CSRF protection, etc.
	 * @return void
	 */
	protected function mapWebRoutes()
	{
		\Route::group([
			'middleware' => 'DummyName',
			'prefix'     => 'DummyName',
		], function (Router $router) {
		});
	}

	/**
	 * Define the "api" routes for the module.
	 * These routes are typically stateless.
	 * @return void
	 */
	protected function mapApiRoutes()
	{
		\Route::group([
			// todo auth
			'prefix' => 'api/DummyName',
		], function (Router $route) {
			require_once poppy_path('DummyName', 'src/routes/web.php');
		});
	}
}
