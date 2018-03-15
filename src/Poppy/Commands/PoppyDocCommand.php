<?php namespace Poppy\Framework\Poppy\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class PoppyDocCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'poppy:doc';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'For Helper Document';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$type = $this->argument('type');
		switch ($type) {
			case 'phpcs';
			case 'cs';
				$this->info(
					'Please Run Command:' . "\n" .
					'php-cs-fixer fix --config=' . pf_path('.php_cs') . ' --diff --dry-run --verbose --diff-format=udiff'
				);
				break;
			case 'phplint':
			case 'lint':
				$lintFile = base_path('vendor/bin/phplint');
				if (file_exists($lintFile)) {
					$this->info('Please Run Command:' . "\n" .
						'./vendor/bin/phplint -c ' . pf_path('.phplint.yml')
					);
				}
				else {
					$this->warn('Please run `composer require overtrue/phplint -vvv` to install phplint');
				}
				break;
			default:
				$this->comment('Type is now allowed.');
				break;
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
			['type', InputArgument::REQUIRED, ' Support Type [phpcs,cs|php-cs-fixer].'],
		];
	}
}
