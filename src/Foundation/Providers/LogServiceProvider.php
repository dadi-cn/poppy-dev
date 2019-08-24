<?php namespace Poppy\Framework\Foundation\Providers;

use Illuminate\Log\LogServiceProvider as LogServiceProviderBase;
use Illuminate\Log\Writer;

class LogServiceProvider extends LogServiceProviderBase
{
	/**
	 * Configure the Monolog handlers for the application.
	 *
	 * @param Writer $log
	 */
	protected function configureSingleHandler(Writer $log)
	{
		$log->useFiles(
			$this->app->storagePath() . '/logs/system.log',
			$this->logLevel()
		);
	}

	/**
	 * Configure the Monolog handlers for the application.
	 *
	 * @param Writer $log
	 */
	protected function configureDailyHandler(Writer $log)
	{
		$log->useDailyFiles(
			$this->app->storagePath() . '/logs/system.log',
			$this->maxFiles(),
			$this->logLevel()
		);
	}
}
