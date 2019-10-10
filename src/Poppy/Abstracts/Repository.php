<?php namespace Poppy\Framework\Poppy\Abstracts;

use Exception;
use File;
use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Poppy\Contracts\Repository as RepositoryContract;

abstract class Repository implements RepositoryContract
{
	/**
	 * @var Config
	 */
	protected $config;

	/**
	 * @var Filesystem
	 */
	protected $files;

	/**
	 * @var string Path to the defined modules directory
	 */
	protected $path;

	/**
	 * Constructor method.
	 * @param Config     $config
	 * @param Filesystem $files
	 */
	public function __construct(Config $config, Filesystem $files)
	{
		$this->config = $config;
		$this->files  = $files;
	}

	/**
	 * Get a module's manifest contents.
	 * @param string $slug
	 * @return Collection
	 * @throws Exception
	 */
	public function getManifest($slug): Collection
	{
		if (!is_null($slug)) {
			$path     = $this->getManifestPath($slug);
			$contents = $this->files->get($path);
			@json_decode($contents, true);
			if (json_last_error() === JSON_ERROR_NONE) {
				return collect(json_decode($contents, true));
			}
			throw new ApplicationException(
				'[' . $slug . '] Your JSON manifest file was not properly formatted. ' .
				'Check for formatting issues and try again.'
			);
		}
	}

	/**
	 * Get path for the specified module.
	 * @param string $slug
	 * @return string
	 */
	public function getModulePath($slug): string
	{
		// poppy module
		if (str_contains($slug, '.')) {
			$poppyModule = str_after($slug, '.');
			$poppyPath   = app('path.poppy');
			if (File::exists($poppyPath . "/{$poppyModule}/")) {
				return $poppyPath . "/{$poppyModule}/";
			}

			return $poppyPath . "/{$poppyModule}/";
		}

		$module     = studly_case(str_slug($slug));
		$modulePath = app('path.module');
		if (File::exists($modulePath . "/{$module}/")) {
			return $modulePath . "/{$module}/";
		}

		return $modulePath . "/{$slug}/";
	}

	/**
	 * Get modules namespace.
	 * @return string
	 */
	public function getNamespace()
	{
		return rtrim($this->config->get('poppy.namespace'), '/\\');
	}

	/**
	 * Get path of module manifest file.
	 * @param $slug
	 * @return string
	 */
	protected function getManifestPath($slug)
	{
		return $this->getModulePath($slug) . 'manifest.json';
	}

	/**
	 * 获取所有模块的基本名称
	 * Get all module base names.
	 * @return Collection
	 */
	protected function getAllBaseNames(): Collection
	{
		try {
			$collection = collect($this->files->directories(app('path.module')));

			$baseNames = $collection->map(function ($item, $key) {
				return basename($item);
			});

			// poppy path
			$collection = collect($this->files->directories(app('path.poppy')));
			$collection->each(function ($item) use ($baseNames) {
				if ($this->files->exists($item . '/manifest.json')) {
					$baseNames->push('poppy.' . basename($item));
				}
			});
			return $baseNames;
		} catch (InvalidArgumentException $e) {
			return collect([]);
		}
	}
}