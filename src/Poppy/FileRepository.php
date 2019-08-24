<?php namespace Poppy\Framework\Poppy;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Poppy\Abstracts\Repository;
use Poppy\Framework\Poppy\Events\PoppyOptimized;
use function count;

class FileRepository extends Repository
{
	use PoppyTrait;

	/**
	 * Get all modules.
	 * @return Collection
	 * @throws FileNotFoundException
	 */
	public function all()
	{
		return $this->getCache()->sortBy('order');
	}

	/**
	 * Get all module slugs.
	 * @return Collection
	 * @throws FileNotFoundException
	 */
	public function slugs()
	{
		$slugs = collect();

		$this->all()->each(function ($item) use ($slugs) {
			$slugs->push(strtolower($item['slug']));
		});

		return $slugs;
	}

	/**
	 * Get modules based on where clause.
	 * @param string $key
	 * @param mixed  $value
	 * @return Collection
	 * @throws FileNotFoundException
	 */
	public function where($key, $value)
	{
		return collect($this->all()->where($key, $value)->first());
	}

	/**
	 * Sort modules by given key in ascending order.
	 * @param string $key
	 * @return Collection
	 * @throws FileNotFoundException
	 */
	public function sortBy($key)
	{
		$collection = $this->all();

		return $collection->sortBy($key);
	}

	/**
	 * Sort modules by given key in ascending order.
	 * @param string $key
	 * @return Collection
	 * @throws FileNotFoundException
	 */
	public function sortByDesc($key)
	{
		$collection = $this->all();

		return $collection->sortByDesc($key);
	}

	/**
	 * Determines if the given module exists.
	 * @param string $slug
	 * @return bool
	 * @throws FileNotFoundException
	 */
	public function exists($slug)
	{
		return $this->slugs()->contains(str_slug($slug));
	}

	/**
	 * Returns count of all modules.
	 * @return int
	 * @throws FileNotFoundException
	 */
	public function count()
	{
		return $this->all()->count();
	}

	/**
	 * Get a module property value.
	 * @param string $property
	 * @param mixed  $default
	 * @return mixed
	 * @throws FileNotFoundException
	 */
	public function get($property, $default = null)
	{
		[$slug, $key] = explode('::', $property);

		$module = $this->where('slug', $slug);

		return $module->get($key, $default);
	}

	/**
	 * Set the given module property value.
	 * @param string $property
	 * @param mixed  $value
	 * @return bool
	 * @throws FileNotFoundException
	 */
	public function set($property, $value)
	{
		[$slug, $key] = explode('::', $property);

		$cachePath = $this->getCachePath();
		$cache     = $this->getCache();
		$module    = $this->where('slug', $slug);

		if (isset($module[$key])) {
			unset($module[$key]);
		}

		$module[$key] = $value;

		$module = collect([$module['basename'] => $module]);

		$merged  = $cache->merge($module);
		$content = json_encode($merged->all(), JSON_PRETTY_PRINT);

		return $this->files->put($cachePath, $content);
	}

	/**
	 * Get all enabled modules.
	 * @return Collection
	 * @throws FileNotFoundException
	 */
	public function enabled()
	{
		return $this->all()->where('enabled', true);
	}

	/**
	 * Get all disabled modules.
	 * @return Collection
	 * @throws FileNotFoundException
	 */
	public function disabled()
	{
		return $this->all()->where('enabled', false);
	}

	/**
	 * Check if specified module is enabled.
	 * @param string $slug
	 * @return bool
	 * @throws FileNotFoundException
	 */
	public function isEnabled($slug)
	{
		$module = $this->where('slug', $slug);

		return $module['enabled'] === true;
	}

	/**
	 * Check if specified module is disabled.
	 * @param string $slug
	 * @return bool
	 * @throws FileNotFoundException
	 */
	public function isDisabled($slug)
	{
		$module = $this->where('slug', $slug);

		return $module['enabled'] === false;
	}

	/**
	 * @param $slug
	 * @return bool
	 * @throws FileNotFoundException
	 */
	public function isInstalled($slug)
	{
		$module = $this->where('slug', $slug);

		return isset($module['installed']) && $module['installed'] === false;
	}

	/**
	 * @param $slug
	 * @return bool
	 * @throws FileNotFoundException
	 */
	public function install($slug)
	{
		return $this->set($slug . '::installed', true);
	}

	/**
	 * @param $slug
	 * @return bool
	 * @throws FileNotFoundException
	 */
	public function uninstall($slug)
	{
		return $this->set($slug . '::installed', false);
	}

	/**
	 * Enables the specified module.
	 * @param string $slug
	 * @return bool
	 * @throws FileNotFoundException
	 */
	public function enable($slug)
	{
		return $this->set($slug . '::enabled', true);
	}

	/**
	 * Disables the specified module.
	 * @param string $slug
	 * @return bool
	 * @throws FileNotFoundException
	 */
	public function disable($slug)
	{
		return $this->set($slug . '::enabled', false);
	}

	/*
	|--------------------------------------------------------------------------
	| Optimization Methods
	|--------------------------------------------------------------------------
	|
	*/

	/**
	 * Update cached repository of module information.
	 * @return bool
	 * @throws FileNotFoundException
	 * @throws ApplicationException
	 */
	public function optimize()
	{
		$cachePath = $this->getCachePath();
		$cache     = $this->getCache();
		$baseNames = $this->getAllBasenames();
		$modules   = collect();

		$baseNames->each(function ($module) use ($modules, $cache) {
			$basename = collect(['basename' => $module]);
			$temp     = $basename->merge(collect($cache->get($module)));
			$manifest = $temp->merge(collect($this->getManifest($module)));
			$modules->put($module, $manifest);
		});

		$depends = '';
		$modules->each(function (Collection $module) use (&$depends) {
			$module->put('id', crc32($module->get('slug')));

			if (!$module->has('enabled')) {
				$module->put('enabled', config('modules.enabled', true));
			}

			if (!$module->has('order')) {
				$module->put('order', 9001);
			}

			$dependencies = (array) $module->get('dependencies');

			if (count($dependencies)) {
				foreach ($dependencies as $dependency) {
					$class = $dependency['class'];
					if (!class_exists($class)) {
						$depends .=
							'You need to install `' . $dependency['package'] . '` (' . $dependency['description'] . ')';
					}
				}
			}

			return $module;
		});

		if ($depends) {
			throw new ApplicationException($depends);
		}

		$content = json_encode($modules->all(), JSON_PRETTY_PRINT);

		$result = $this->files->put($cachePath, $content);

		$this->getEvent()->dispatch(new PoppyOptimized($modules->all()));

		return $result;
	}

	/**
	 * Get the contents of the cache file.
	 * @return Collection
	 * @throws FileNotFoundException
	 */
	private function getCache()
	{
		$cachePath = $this->getCachePath();

		if (!$this->files->exists($cachePath)) {
			$this->createCache();

			$this->optimize();
		}

		return collect(json_decode($this->files->get($cachePath), true));
	}

	/**
	 * Create an empty instance of the cache file.
	 * @return Collection
	 */
	private function createCache()
	{
		$cachePath = $this->getCachePath();
		$content   = json_encode([], JSON_PRETTY_PRINT);

		$this->files->put($cachePath, $content);

		return collect(json_decode($content, true));
	}

	/**
	 * Get the path to the cache file.
	 * @return string
	 */
	private function getCachePath()
	{
		return storage_path('app/poppy.json');
	}
}

