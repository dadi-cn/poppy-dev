<?php namespace Poppy\Framework\Poppy\Abstracts;

use Exception;
use File;
use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Poppy\Framework\Poppy\Contracts\Repository as RepositoryContract;

/**
 * Repository
 */
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
	 * @param Config     $config config
	 * @param Filesystem $files  files
	 */
	public function __construct(Config $config, Filesystem $files)
	{
		$this->config = $config;
		$this->files  = $files;
	}

	/**
	 * Get a module's manifest contents.
	 * @param string $slug slug
	 * @return Collection
	 * @throws Exception
	 */
	public function getManifest($slug)
	{
		if (!is_null($slug)) {
			$path     = $this->getManifestPath($slug);
			$contents = $this->files->get($path);
			@json_decode($contents, true);
			if (json_last_error() === JSON_ERROR_NONE) {
				$collection = collect(json_decode($contents, true));

				return $collection;
			}
			throw new Exception(
				'[' . $slug . '] Your JSON manifest file was not properly formatted. ' .
				'Check for formatting issues and try again.'
			);
		}
	}

	/**
	 * Get modules path.
	 * @return string
	 */
	public function getPath()
	{
		return $this->path ?: app('path.module');
	}

	/**
	 * Set modules path in "RunTime" mode.
	 * @param string $path path
	 * @return object $this
	 */
	public function setPath($path)
	{
		$this->path = $path;

		return $this;
	}

	/**
	 * Get path for the specified module.
	 * @param string $slug slug
	 * @return string
	 */
	public function getModulePath($slug)
	{
		$module = studly_case(str_slug($slug));

		if (File::exists($this->getPath() . "/{$module}/")) {
			return $this->getPath() . "/{$module}/";
		}

		return $this->getPath() . "/{$slug}/";
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
	 * @param string $slug $slug
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
	protected function getAllBaseNames()
	{
		$path = $this->getPath();

		try {
			$collection = collect($this->files->directories($path));

			$baseNames = $collection->map(function ($item, $key) {
				return basename($item);
			});

			return $baseNames;
		} catch (InvalidArgumentException $e) {
			return collect([]);
		}
	}
}