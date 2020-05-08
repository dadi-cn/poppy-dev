<?php namespace Poppy\Framework\Console\Generators;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;
use Poppy\Framework\Console\GeneratorCommand;

/**
 * Make Listener
 */
class MakeListenerCommand extends GeneratorCommand
{
	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'poppy:listener
    	{slug : The slug of the module.}
    	{name : The name of the model class.}
    	{--E|event= : The event class being listened for}
    	{--Q|queued : Indicates the event listener should be queued}
    	';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Create a new module event class';

	/**
	 * String to store the command type.
	 * @var string
	 */
	protected $type = 'Module listener';


	/**
	 * Get the stub file for the generator.
	 * @return string
	 */
	protected function getStub()
	{
		if ($this->option('queued')) {
			return $this->option('event')
				? __DIR__ . '/stubs/listener-queued.stub'
				: __DIR__ . '/stubs/listener-queued-duck.stub';
		}

		return $this->option('event')
			? __DIR__ . '/stubs/listener.stub'
			: __DIR__ . '/stubs/listener-duck.stub';
	}


	/**
	 * Build the class with the given name.
	 *
	 * @param string $name
	 * @return string
	 * @throws FileNotFoundException
	 */
	protected function buildClass($name)
	{
		$event = $this->option('event');


		if (Str::startsWith($event, '\\')) {
			// event with full
			$fullEvent = $event;
		}
		else {
			// event with module
			$fullEvent = poppy_class($this->argument('slug'), 'Events\\' . $event);
		}


		$stub = str_replace(
			'DummyEvent', class_basename($fullEvent), parent::buildClass($name)
		);

		return str_replace(
			'DummyFullEvent', trim($fullEvent, '\\'), $stub
		);
	}

	/**
	 * Get the default namespace for the class.
	 * @param string $rootNamespace namespace
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		return poppy_class($this->argument('slug'), 'Listeners');
	}
}
