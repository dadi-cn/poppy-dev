<?php namespace Poppy\Framework\Update\Migration;

use Closure;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Filesystem\Filesystem;

class MigrationCreator
{
	/**
	 * The filesystem instance.
	 *
	 * @var Filesystem
	 */
	protected $files;

	/**
	 * The migrator
	 * @var Migrator
	 */
	protected $migrator;

	/**
	 * The registered post create hooks.
	 *
	 * @var array
	 */
	protected $postCreate = [];

	/**
	 * Create a new migration creator instance.
	 *
	 * @param Filesystem $files
	 * @param Migrator   $migrator
	 */
	public function __construct(Filesystem $files, Migrator $migrator)
	{
		$this->files    = $files;
		$this->migrator = $migrator;
	}

	/**
	 * Create a new migration at the given path.
	 *
	 * @param  string $slug
	 * @param  string $name
	 * @return string
	 * @throws \Exception
	 */
	public function create($slug, $name): string
	{
		$this->ensureMigrationDoesntAlreadyExist($slug, $name);

		// First we will get the stub file for the migration, which serves as a type
		// of template for the migration. Once we have those we will populate the
		// various place-holders, save the file, and run the post create event.
		$stub = $this->getStub();
		$path = $this->getPath($slug, $name);

		if (!$this->files->isDirectory(\dirname($path))) {
			$this->files->makeDirectory(\dirname($path), 0755, true);
		}
		$this->files->put(
			$path,
			$this->populateStub($slug, $name, $stub)
		);

		// Next, we will fire any hooks that are supposed to fire after a migration is
		// created. Once that is done we'll be ready to return the full path to the
		// migration file so it can be used however it's needed by the developer.
		$this->firePostCreateHooks();

		return $path;
	}

	/**
	 * Ensure that a migration with the given name doesn't already exist.
	 *
	 * @param  string $name
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function ensureMigrationDoesntAlreadyExist($slug, $name)
	{
		// {Slug}{Name}Update
		$files = $this->files->files(poppy_path($slug, 'src/update/'));
		$this->migrator->requireFiles($files);
		if (class_exists($className = $this->getClassName($slug, $name))) {
			throw new InvalidArgumentException("A {$className} class already exists.");
		}
	}

	/**
	 * Get the migration stub file.
	 * @return string
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	protected function getStub(): string
	{
		return $this->files->get($this->stubPath() . '/update.stub');
	}

	/**
	 * Populate the place-holders in the migration stub.
	 *
	 * @param  string $slug
	 * @param  string $name
	 * @param  string $stub
	 * @return string
	 */
	protected function populateStub($slug, $name, $stub)
	{
		$className = $this->getClassName($slug, $name);
		$stub      = str_replace(
			['DummyClass'],
			[$className],
			$stub);
		return $stub;
	}

	/**
	 * Get the class name of a migration name.
	 *
	 * @param  string $slug
	 * @param  string $name
	 * @return string
	 */
	protected function getClassName($slug, $name): string
	{
		return Str::studly($slug . '_' . $name . '_update');
	}

	/**
	 * Get the full path to the migration.
	 *
	 * @param         $slug
	 * @param  string $name
	 * @return string
	 * @throws \Poppy\Framework\Exceptions\ModuleNotFoundException
	 */
	protected function getPath($slug, $name): string
	{
		$className = str_replace('\\', '', $this->getClassName($slug, $name));
		return poppy_path($slug, 'src/update/' . $this->getDatePrefix() . '_' . Str::snake($className) . '.php');
	}

	/**
	 * Get the date prefix for the migration.
	 *
	 * @return string
	 */
	protected function getDatePrefix(): string
	{
		return date('Y_m_d_His');
	}

	/**
	 * Fire the registered post create hooks.
	 *
	 * @return void
	 */
	protected function firePostCreateHooks()
	{
		foreach ($this->postCreate as $callback) {
			call_user_func($callback);
		}
	}

	/**
	 * Register a post migration create hook.
	 *
	 * @param  \Closure $callback
	 * @return void
	 */
	public function afterCreate(Closure $callback)
	{
		$this->postCreate[] = $callback;
	}

	/**
	 * Get the path to the stubs.
	 *
	 * @return string
	 */
	public function stubPath()
	{
		return __DIR__ . '/stubs';
	}

	/**
	 * Get the filesystem instance.
	 *
	 * @return \Illuminate\Filesystem\Filesystem
	 */
	public function getFilesystem()
	{
		return $this->files;
	}
}
