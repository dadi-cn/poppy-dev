<?php namespace Poppy\Framework\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Poppy Enable
 */
class PoppyEnableCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'poppy:enable';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Enable a module';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$slug = $this->argument('slug');

		if ($this->laravel['poppy']->isDisabled($slug)) {
			$this->laravel['poppy']->enable($slug);

			$module = $this->laravel['poppy']->where('slug', $slug);

			event($slug . '.poppy.enabled', [$module, null]);

			$this->info('Module was enabled successfully.');
		}
		else {
			$this->comment('Module is already enabled.');
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['slug', InputArgument::REQUIRED, 'Module slug.'],
		];
	}
}
