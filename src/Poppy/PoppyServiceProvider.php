<?php namespace Poppy\Framework\Poppy;

use Illuminate\Support\ServiceProvider;
use Poppy\Framework\Poppy\Commands\PoppyDocCommand;
use Poppy\Framework\Poppy\Contracts\Repository;

/**
 * module manager
 */
class PoppyServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 */
	public function boot()
	{
	}

	/**
	 * Register the application services.
	 */
	public function register()
	{
		$this->app->bind('Poppy\Framework\Poppy\Contracts\Repository', FileRepository::class);

		$this->app->singleton('poppy', function ($app) {
			$repository = $app->make(Repository::class);

			return new Poppy($app, $repository);
		});

		$this->commands([
			PoppyDocCommand::class,
		]);
	}

	/**
	 * Get the services provided by the provider.
	 * @return array
	 */
	public function provides()
	{
		// return ['duoli.form'];
		return ['poppy'];
	}
}
