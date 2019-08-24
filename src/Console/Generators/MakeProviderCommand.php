<?php namespace Poppy\Framework\Console\Generators;

use Poppy\Framework\Console\GeneratorCommand;
use Poppy\Framework\Exceptions\ModuleNotFoundException;

class MakeProviderCommand extends GeneratorCommand
{
	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'poppy:provider
    	{slug : The slug of the module.}
    	{name : The name of the service provider class.}';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Create a new module service provider class';

	/**
	 * String to store the command type.
	 * @var string
	 */
	protected $type = 'Module service provider';

	/**
	 * Get the stub file for the generator.
	 * @return string
	 */
	protected function getStub()
	{
		return __DIR__ . '/stubs/provider.stub';
	}

	/**
	 * Get the default namespace for the class.
	 * @param string $rootNamespace
	 * @return string
	 * @throws ModuleNotFoundException
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		return poppy_class($this->argument('slug'), 'providers');
	}
}
