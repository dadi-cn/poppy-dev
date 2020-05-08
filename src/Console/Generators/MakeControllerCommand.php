<?php namespace Poppy\Framework\Console\Generators;

use Illuminate\Support\Str;
use Poppy\Framework\Console\GeneratorCommand;
use Poppy\Framework\Exceptions\ModuleNotFoundException;

/**
 * MakeController
 */
class MakeControllerCommand extends GeneratorCommand
{
	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'poppy:controller
    	{slug : The slug of the module}
    	{type : The type of the controller class}
    	{name : The name of the controller class}
    	{--resource : Generate a module resource controller class}';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Create a new poppy module controller class';

	/**
	 * String to store the command type.
	 * @var string
	 */
	protected $type = 'Poppy Module controller';

	/**
	 * Get the stub file for the generator.
	 * @return string
	 */
	protected function getStub()
	{
		if ($this->option('resource')) {
			return __DIR__ . '/stubs/controller.resource.stub';
		}

		return __DIR__ . '/stubs/controller.stub';
	}

	/**
	 * Get the default namespace for the class.
	 * @param string $rootNamespace namespace
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		$type = $this->argument('type');

		if (!in_array($type, ['web', 'api'])) {
			$type = 'web';
		}

		return poppy_class($this->argument('slug'), 'Http\\Request\\' . Str::studly($type));
	}
}
