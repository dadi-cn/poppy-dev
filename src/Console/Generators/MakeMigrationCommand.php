<?php namespace Poppy\Framework\Console\Generators;

use Illuminate\Console\Command;

class MakeMigrationCommand extends Command
{
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
	 * @throws \Poppy\Framework\Exceptions\ModuleNotFoundException
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
			poppy_path($this->argument('slug'), 'src/database/migrations')
		);
		$options['--path'] = ltrim($options['--path'], '/');

		var_dump(array_merge($arguments, $options));

		$this->call('make:migration', array_merge($arguments, $options));
	}
}
