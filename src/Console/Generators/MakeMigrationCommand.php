<?php namespace Poppy\Framework\Console\Generators;

use Illuminate\Console\Command;
use Poppy\Framework\Classes\Traits\MigrationTrait;
use Poppy\Framework\Exceptions\ModuleNotFoundException;

/**
 * Make Migration
 */
class MakeMigrationCommand extends Command
{

	use MigrationTrait;

	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'poppy:migration
    	{slug : The slug of the module.}
    	{name : The name of the migration.}
    	{--create= : The table to be created.}
        {--table= : The table to migrate.}';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Create a new module migration file';

	/**
	 * Execute the console command.
	 * @return int|void
	 * @throws ModuleNotFoundException
	 */
	public function handle()
	{
		$arguments = $this->argument();
		$option    = $this->option();
		$options   = [];

		array_walk($option, function (&$value, $key) use (&$options) {
			$options['--' . $key] = $value;
		});

		unset($arguments['slug']);

		$options['--path'] = str_replace(
			realpath(base_path()),
			'',
			$this->getMigrationPath($this->argument('slug'))
		);
		$options['--path'] = ltrim($options['--path'], '/');

		if (!app('files')->exists(base_path($options['--path']))) {
			$this->error('Path `' . $options['--path'] . '` not exists');
			return;
		}

		$this->call('make:migration', array_merge($arguments, $options));
	}
}
