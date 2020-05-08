<?php namespace Poppy\Framework\Console;

use Illuminate\Support\ServiceProvider;
use Poppy\Framework\Console\Generators\MakeCommandCommand;
use Poppy\Framework\Console\Generators\MakeControllerCommand;
use Poppy\Framework\Console\Generators\MakeEventCommand;
use Poppy\Framework\Console\Generators\MakeListenerCommand;
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
	 * Register the application services.
	 */
	public function register()
	{
		$this->commands([
			MakePoppyCommand::class,
			MakeControllerCommand::class,
			MakeMiddlewareCommand::class,
			MakeMigrationCommand::class,
			MakeModelCommand::class,
			MakePolicyCommand::class,
			MakeProviderCommand::class,
			MakeRequestCommand::class,
			MakeSeederCommand::class,
			MakeTestCommand::class,
			MakeCommandCommand::class,
			MakeEventCommand::class,
			MakeListenerCommand::class,
		]);
	}
}
