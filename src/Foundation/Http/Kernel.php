<?php namespace Poppy\Framework\Foundation\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Poppy\Framework\Http\Middlewares\EnableCrossRequest;

class Kernel extends HttpKernel
{
	/**
	 * The bootstrap classes for the application.
	 * @var array
	 */
	protected $bootstrappers = [
		'Poppy\Framework\Foundation\Bootstrap\RegisterClassLoader',   // poppy class loader
		'Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables',
		'Illuminate\Foundation\Bootstrap\LoadConfiguration',
		'Illuminate\Foundation\Bootstrap\HandleExceptions',
		'Illuminate\Foundation\Bootstrap\RegisterFacades',
		'Illuminate\Foundation\Bootstrap\RegisterProviders',
		'Illuminate\Foundation\Bootstrap\BootProviders',
	];

	/**
	 * The application's global HTTP middleware stack.
	 * @var array
	 */
	protected $middleware = [
		'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode',
		'Illuminate\Foundation\Http\Middleware\ValidatePostSize',
		'Illuminate\Foundation\Http\Middleware\TrimStrings',
		'Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull',
	];

	/**
	 * The application's route middleware.
	 * @var array
	 */
	protected $routeMiddleware = [
		// 'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
		'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
		// 'can' => \Illuminate\Auth\Middleware\Authorize::class,
		// 'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
		'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
		'cross'    => EnableCrossRequest::class,
	];

	/**
	 * The application's route middleware groups.
	 * @var array
	 */
	protected $middlewareGroups = [
		'web' => [
			\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
			\Illuminate\Session\Middleware\StartSession::class,
			\Illuminate\View\Middleware\ShareErrorsFromSession::class,
			// 'System\Request\Middleware\VerifyCsrfToken',
			\Illuminate\Routing\Middleware\SubstituteBindings::class,
		],
		'api' => [
			'throttle:60,1',
			'bindings',
		],
	];
}