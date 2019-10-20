<?php namespace Poppy\Framework\Poppy\Contracts;

use Illuminate\Support\Collection;

/**
 * Repository
 */
interface Repository
{
	/**
	 * Get all module manifest properties and store
	 * in the respective container.
	 *
	 * @return bool
	 */
	public function optimize();

	/**
	 * Get all modules.
	 *
	 * @return Collection
	 */
	public function all(): Collection;

	/**
	 * Get all module slugs.
	 *
	 * @return Collection
	 */
	public function slugs();

	/**
	 * Get modules based on where clause.
	 *
	 * @param string $key   key
	 * @param mixed  $value value
	 *
	 * @return Collection
	 */
	public function where($key, $value);

	/**
	 * Sort modules by given key in ascending order.
	 *
	 * @param string $key key
	 *
	 * @return Collection
	 */
	public function sortBy($key);

	/**
	 * Sort modules by given key in descending order.
	 *
	 * @param string $key key
	 *
	 * @return Collection
	 */
	public function sortByDesc($key);

	/**
	 * Determines if the given module exists.
	 *
	 * @param string $slug slug
	 *
	 * @return bool
	 */
	public function exists($slug);

	/**
	 * Returns a count of all modules.
	 *
	 * @return int
	 */
	public function count();

	/**
	 * Returns the modules defined manifest properties.
	 *
	 * @param string $slug slug
	 *
	 * @return Collection
	 */
	public function getManifest($slug);

	/**
	 * Returns the given module property.
	 *
	 * @param string     $property property
	 * @param mixed|null $default  default
	 *
	 * @return mixed|null
	 */
	public function get($property, $default = null);

	/**
	 * Set the given module property value.
	 *
	 * @param string $property property
	 * @param mixed  $value    value
	 *
	 * @return bool
	 */
	public function set($property, $value);

	/**
	 * Get all enabled modules.
	 *
	 * @return Collection
	 */
	public function enabled();

	/**
	 * Get all disabled modules.
	 *
	 * @return Collection
	 */
	public function disabled();

	/**
	 * Determines if the specified module is enabled.
	 *
	 * @param string $slug slug
	 *
	 * @return bool
	 */
	public function isEnabled($slug);

	/**
	 * Determines if the specified module is disabled.
	 *
	 * @param string $slug slug
	 *
	 * @return bool
	 */
	public function isDisabled($slug);

	/**
	 * Enables the specified module.
	 *
	 * @param string $slug slug
	 *
	 * @return bool
	 */
	public function enable($slug);

	/**
	 * Disables the specified module.
	 *
	 * @param string $slug slug
	 *
	 * @return bool
	 */
	public function disable($slug);
}
