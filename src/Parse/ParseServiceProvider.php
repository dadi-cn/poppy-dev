<?php namespace Poppy\Framework\Parse;

use Illuminate\Support\ServiceProvider;

/**
 * ParseServiceProvider
 */
class ParseServiceProvider extends ServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('poppy.yaml', function ($app) {
			return new Yaml();
		});

		$this->app->singleton('poppy.ini', function ($app) {
			return new Ini();
		});

		$this->app->singleton('poppy.xml', function ($app) {
			return new Xml();
		});
	}

	/**
	 * Get the services provided by the provider.
	 * @return array
	 */
	public function provides()
	{
		return [
			'poppy.yaml',
			'poppy.ini',
			'poppy.xml',
		];
	}
}
