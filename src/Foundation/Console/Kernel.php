<?php namespace Poppy\Framework\Foundation\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Bootstrap\BootProviders;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Foundation\Bootstrap\RegisterFacades;
use Illuminate\Foundation\Bootstrap\RegisterProviders;
use Illuminate\Foundation\Bootstrap\SetRequestForConsole;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Poppy\Framework\Foundation\Bootstrap\RegisterClassLoader;

/**
 * poppy console kernel
 */
class Kernel extends ConsoleKernel
{
	/**
	 * @var array bootstrappers
	 */
	protected $bootstrappers = [
		RegisterClassLoader::class,   // poppy module loader
		LoadEnvironmentVariables::class,
		LoadConfiguration::class,
		HandleExceptions::class,
		RegisterFacades::class,
		SetRequestForConsole::class,
		RegisterProviders::class,
		BootProviders::class,
	];

	/**
	 * 定义计划命令
	 * @param Schedule $schedule schedule
	 */
	protected function schedule(Schedule $schedule)
	{
		$this->app['events']->dispatch('console.schedule', [$schedule]);
	}
}