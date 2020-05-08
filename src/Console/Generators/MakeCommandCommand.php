<?php namespace Poppy\Framework\Console\Generators;

use Poppy\Framework\Console\GeneratorCommand;
use Poppy\Framework\Exceptions\ModuleNotFoundException;

/**
 * MakeCommand
 */
class MakeCommandCommand extends GeneratorCommand
{
	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'poppy:command
    	{slug : The slug of the module}
    	{name : The name of the command name}';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Create a new command class';

	/**
	 * String to store the command type.
	 * @var string
	 */
	protected $type = 'Module test';

	/**
	 * Get the stub file for the generator.
	 * @return string
	 */
	protected function getStub()
	{
		return __DIR__ . '/stubs/command.stub';
	}

	/**
	 * Get the default namespace for the class.
	 * @param string $rootNamespace namespace
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		return poppy_class($this->argument('slug'), 'Commands');
	}
}
