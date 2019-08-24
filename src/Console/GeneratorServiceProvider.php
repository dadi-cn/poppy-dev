<?php namespace Poppy\Framework\Console;

use Illuminate\Support\ServiceProvider;
use Poppy\Framework\Console\Generators\MakeCommandCommand;
use Poppy\Framework\Console\Generators\MakeControllerCommand;
use Poppy\Framework\Console\Generators\MakeMiddlewareCommand;
use Poppy\Framework\Console\Generators\MakeMigrationCommand;
use Poppy\Framework\Console\Generators\MakeModelCommand;
use Poppy\Framework\Console\Generators\MakePolicyCommand;
use Poppy\Framework\Console\Generators\MakePoppyCommand;
use Poppy\Framework\Console\Generators\MakeProviderCommand;
use Poppy\Framework\Console\Generators\MakeRequestCommand;
use Poppy\Framework\Console\Generators\MakeSeederCommand;
use Poppy\Framework\Console\Generators\MakeTestCommand;

class GeneratorServiceProvider extends ServiceProvider
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
		$generators = [
			'command.make.poppy'            => MakePoppyCommand::class,
			'command.make.poppy.controller' => MakeControllerCommand::class,
			'command.make.poppy.middleware' => MakeMiddlewareCommand::class,
			'command.make.poppy.migration'  => MakeMigrationCommand::class,
			'command.make.poppy.model'      => MakeModelCommand::class,
			'command.make.poppy.policy'     => MakePolicyCommand::class,
			'command.make.poppy.provider'   => MakeProviderCommand::class,
			'command.make.poppy.request'    => MakeRequestCommand::class,
			'command.make.poppy.seeder'     => MakeSeederCommand::class,
			'command.make.poppy.test'       => MakeTestCommand::class,
			'command.make.poppy.command'    => MakeCommandCommand::class,
		];

		foreach ($generators as $slug => $class) {
			$this->app->singleton($slug, function ($app) use ($slug, $class) {
				return $app[$class];
			});

			$this->commands($slug);
		}
	}
}
