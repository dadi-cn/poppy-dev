<?php namespace Poppy\Framework\Console;

use Illuminate\Console\GeneratorCommand as LaravelGeneratorCommand;
use Illuminate\Support\Str;
use Poppy\Framework\Exceptions\ModuleNotFoundException;

/**
 * Poppy Generator Command
 */
abstract class GeneratorCommand extends LaravelGeneratorCommand
{
	/**
	 * Parse the name and format according to the root namespace.
	 * @param string $name name
	 * @return string
	 */
	protected function parseName($name)
	{
		$rootNamespace = '';
		if (Str::startsWith($name, $rootNamespace)) {
			return $name;
		}

		if (Str::contains($name, '/')) {
			$name = str_replace('/', '\\', $name);
		}

		return $this->parseName($this->getDefaultNamespace(trim($rootNamespace, '\\')) . '\\' . $name);
	}

	/**
	 * Parse the class name and format according to the root namespace.
	 * @param string $name classname
	 * @return string
	 */
	protected function qualifyClass($name)
	{
		$name = Str::studly(ltrim($name, '\\/'));

		$name = str_replace('/', '\\', $name);

		return $this->getDefaultNamespace('') . '\\' . $name;
	}

	/**
	 * Get the destination class path.
	 * @param string $name full class name
	 * @return string
	 * @throws ModuleNotFoundException
	 */
	protected function getPath($name)
	{
		$slug = $this->argument('slug');

		if (!$this->laravel['poppy']->exists($slug)) {
			throw new ModuleNotFoundException($slug);
		}

		// take everything after the module name in the given path (ignoring case)
		$trimSlug = Str::after($slug, 'module.');
		$key      = array_search(strtolower($trimSlug), explode('\\', strtolower($name)));
		if ($key === false) {
			$newPath = str_replace('\\', '/', $name);
		}
		else {
			$newPath = implode('/', array_slice(explode('\\', $name), $key + 1));
		}

		$newPath = "{$newPath}.php";

		$pathInfo = pathinfo($newPath);

		if (strpos($pathInfo['dirname'], 'Tests') !== false) {
			$addSrc = '';
		}
		else {
			$addSrc = 'src' . DIRECTORY_SEPARATOR;
		}

		$dirs      = explode('/', $pathInfo['dirname']);
		$lowerDirs = array_map(function ($item) {
			return Str::snake($item);
		}, $dirs);
		$dirname   = implode('/', $lowerDirs);
		return poppy_path(
			$slug,
			$addSrc .
			$dirname . DIRECTORY_SEPARATOR .
			$pathInfo['basename']
		);
	}

	/**
	 * Replace the namespace for the given stub.
	 * @param string $stub stub file
	 * @param string $name name
	 * @return $this
	 */
	protected function replaceNamespace(&$stub, $name)
	{
		$stub = str_replace(
			['DummyNamespace'],
			[$this->getNamespace($name)],
			$stub
		);

		return $this;
	}
}
