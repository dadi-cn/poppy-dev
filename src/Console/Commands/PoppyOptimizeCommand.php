<?php namespace Poppy\Framework\Console\Commands;

use Illuminate\Console\Command;

/**
 * Poppy Optimize
 */
class PoppyOptimizeCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'poppy:optimize';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Optimize the module cache for better performance';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$this->info('Generating optimized module cache');

		$this->laravel['poppy']->optimize();

		event('poppy.optimized', [$this->laravel['poppy']->all()]);
	}
}
