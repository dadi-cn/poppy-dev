<?php namespace Poppy\Framework\Console\Generators;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;
use Poppy\Framework\Console\GeneratorCommand;

/**
 * Make Model
 */
class MakeModelCommand extends GeneratorCommand
{
	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'poppy:model
    	{slug : The slug of the module.}
    	{name : The name of the model class.}
        {--migration : Create a new migration file for the model.}';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Create a new module model class';

	/**
	 * String to store the command type.
	 * @var string
	 */
	protected $type = 'Module model';

	/**
	 * Execute the console command.
	 * @return void
	 * @throws FileNotFoundException
	 */
	public function handle()
	{
		if (parent::handle() !== false) {
			if ($this->option('migration')) {
				$table = Str::plural(Str::snake(class_basename($this->argument('name'))));

				$this->call('poppy:migration', [
					'slug'     => $this->argument('slug'),
					'name'     => "create_{$table}_table",
					'--create' => $table,
				]);
			}
		}
	}

	/**
	 * Get the stub file for the generator.
	 * @return string
	 */
	protected function getStub()
	{
		return __DIR__ . '/stubs/model.stub';
	}

	/**
	 * Get the default namespace for the class.
	 * @param string $rootNamespace namespace
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		return poppy_class($this->argument('slug'), 'Models');
	}
}
