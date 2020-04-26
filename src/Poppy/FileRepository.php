<?php namespace Poppy\Framework\Poppy;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Poppy\Abstracts\Repository;
use Poppy\Framework\Poppy\Events\PoppyOptimized;
use Throwable;

/**
 * FileRepository
 */
class FileRepository extends Repository
{

	/**
	 * Get all modules.
	 * @return Collection
	 * @throws ApplicationException
	 */
	public function all(): Collection
	{
		return $this->getCache()->sortBy('order');
	}

	/**
	 * Get all module slugs.
	 * @return Collection
	 * @throws ApplicationException
	 */
	public function slugs(): Collection
	{
		$slugs = collect();

		$this->all()->each(function ($item) use ($slugs) {
			$slugs->push(strtolower($item['slug']));
		});

		return $slugs;
	}

	/**
	 * Get modules based on where clause.
	 * @param string $key   the filter key
	 * @param mixed  $value value
	 * @return Collection
	 * @throws ApplicationException
	 */
	public function where($key, $value): Collection
	{
		return collect($this->all()->where($key, $value)->first());
	}

	/**
	 * Sort modules by given key in ascending order.
	 * @param string $key sort by key
	 * @return Collection
	 * @throws ApplicationException
	 */
	public function sortBy($key): Collection
	{
		$collection = $this->all();

		return $collection->sortBy($key);
	}

	/**
	 * Sort modules by given key in ascending order.
	 * @param string $key sort by key
	 * @return Collection
	 * @throws ApplicationException
	 */
	public function sortByDesc($key): Collection
	{
		$collection = $this->all();

		return $collection->sortByDesc($key);
	}

	/**
	 * Determines if the given module exists.
	 * @param string $slug module name
	 * @return bool
	 * @throws ApplicationException
	 */
	public function exists($slug): bool
	{
		return $this->slugs()->contains($slug);
	}

	/**
	 * Returns count of all modules.
	 * @return int
	 * @throws ApplicationException
	 */
	public function count(): int
	{
		return $this->all()->count();
	}

	/**
	 * Get a module property value.
	 * @param string $property module property
	 * @param mixed  $default  default value
	 * @return mixed
	 * @throws ApplicationException
	 */
	public function get($property, $default = null)
	{
		[$slug, $key] = explode('::', $property);

		$module = $this->where('slug', $slug);

		return $module->get($key, $default);
	}

	/**
	 * Set the given module property value.
	 * @param string $property module property
	 * @param mixed  $value    set module
	 * @return bool
	 * @throws ApplicationException
	 */
	public function set($property, $value): bool
	{
		try {
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
			$this->files->put($cachePath, $content);
			return true;
		} catch (Throwable $e) {
			throw new ApplicationException($e->getMessage());
		}
	}

	/**
	 * Get all enabled modules.
	 * @return Collection
	 * @throws ApplicationException
	 */
	public function enabled(): Collection
	{
		return $this->all()->where('enabled', true);
	}

	/**
	 * Get all disabled modules.
	 * @return Collection
	 * @throws ApplicationException
	 */
	public function disabled(): Collection
	{
		return $this->all()->where('enabled', false);
	}

	/**
	 * Check if specified module is enabled.
	 * @param string $slug module name
	 * @return bool
	 * @throws ApplicationException
	 */
	public function isEnabled($slug): bool
	{
		$module = $this->where('slug', $slug);

		return $module['enabled'] === true;
	}

	/**
	 * Check if specified module is disabled.
	 * @param string $slug module name
	 * @return bool
	 * @throws ApplicationException
	 */
	public function isDisabled($slug): bool
	{
		$module = $this->where('slug', $slug);

		return $module['enabled'] === false;
	}

	/**
	 * Enables the specified module.
	 * @param string $slug module name
	 * @return bool
	 * @throws ApplicationException
	 */
	public function enable($slug): bool
	{
		return $this->set($slug . '::enabled', true);
	}

	/**
	 * Disables the specified module.
	 * @param string $slug module name
	 * @return bool
	 * @throws ApplicationException
	 */
	public function disable($slug): bool
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
	 * @throws ApplicationException
	 */
	public function optimize(): bool
	{
		$cachePath = $this->getCachePath();
		$cache     = $this->getCache();
		$baseNames = $this->getAllBasenames();
		$modules   = collect();

		$baseNames->each(function ($module) use ($modules, $cache) {
			$basename = collect([]);
			$temp     = $basename->merge(collect($cache->get($module)));
			$manifest = $temp->merge(collect($this->getManifest($module)));
			// rewrite slug
			$manifest['slug'] = $module;
			$modules->put($module, $manifest);
		});

		$depends = '';
		$modules->each(function (Collection $module) use (&$depends) {
			$module->put('id', crc32($module->get('slug')));

			if (!$module->has('enabled')) {
				$module->put('enabled', true);
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

		$content = json_encode($modules->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

		$this->files->put($cachePath, $content);

		event(new PoppyOptimized(collect($modules->all())));

		return true;
	}

	/**
	 * Get the contents of the cache file.
	 * @return Collection
	 * @throws ApplicationException
	 */
	private function getCache(): Collection
	{
		try {
			$cachePath = $this->getCachePath();

			if (!$this->files->exists($cachePath)) {
				$this->createCache();

				$this->optimize();
			}

			return collect(json_decode($this->files->get($cachePath), true));
		} catch (Throwable $e) {
			throw new ApplicationException($e->getMessage());
		}
	}

	/**
	 * Create an empty instance of the cache file.
	 * @return Collection
	 */
	private function createCache(): Collection
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
	private function getCachePath(): string
	{
		return storage_path('app/poppy.json');
	}
}

