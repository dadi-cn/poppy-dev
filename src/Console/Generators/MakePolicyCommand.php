<?php namespace Poppy\Framework\Console\Generators;

use Poppy\Framework\Console\GeneratorCommand;
use Poppy\Framework\Exceptions\ModuleNotFoundException;

/**
 * Make Policy
 */
class MakePolicyCommand extends GeneratorCommand
{
	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'poppy:policy
    	{slug : The slug of the module.}
    	{name : The name of the policy class.}';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Create a new module policy class';

	/**
	 * String to store the command type.
	 * @var string
	 */
	protected $type = 'Module policy';

	/**
	 * Get the stub file for the generator.
	 * @return string
	 */
	protected function getStub()
	{
		return __DIR__ . '/stubs/policy.stub';
	}

	/**
	 * Get the default namespace for the class.
	 * @param string $rootNamespace namespace
	 * @return string
	 * @throws ModuleNotFoundException
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		return poppy_class($this->argument('slug'), 'Models\Policies');
	}
}
