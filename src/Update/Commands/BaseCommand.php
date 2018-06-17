<?php namespace Poppy\Framework\Update\Commands;

use Illuminate\Console\Command;

class BaseCommand extends Command
{
	/**
	 * Get all of the migration paths.
	 *
	 * @return array
	 */
	protected function getMigrationPaths(): array
	{
		$slugs = app('poppy')->slugs();
		return collect($slugs)->map(function ($slug) {
			return poppy_path($slug, 'src/update/');
		})->all();
	}

	/**
	 * Get migration path
	 *
	 * @param $slug
	 * @return string
	 */
	protected function getMigrationPath($slug)
	{
		try {
			return poppy_path($slug, 'src/update/');
		} catch (\Exception $e) {
			return '';
		}
	}
}