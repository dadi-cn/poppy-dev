<?php namespace Poppy\Framework\Classes\Traits;

use Poppy\Framework\Exceptions\ModuleNotFoundException;

/**
 * MigrationTrait
 */
trait MigrationTrait
{
	/**
	 * Require (once) all migration files for the supplied module.
	 *
	 * @param string $module module
	 * @throws ModuleNotFoundException
	 */
	protected function requireMigrations($module)
	{
		$path = $this->getMigrationPath($module);

		$migrations = $this->laravel['files']->glob($path . '*_*.php');

		foreach ($migrations as $migration) {
			$this->laravel['files']->requireOnce($migration);
		}
	}

	/**
	 * Get migration directory path.
	 *
	 * @param string $module module
	 *
	 * @return string
	 * @throws ModuleNotFoundException
	 */
	protected function getMigrationPath($module)
	{
		return poppy_path($module, 'Database/Migrations');
	}
}

