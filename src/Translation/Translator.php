<?php namespace Poppy\Framework\Translation;

use Illuminate\Contracts\Translation\Loader;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Translation\Translator as IlluminateTranslator;

/**
 * Class Translator.
 */
class Translator extends IlluminateTranslator
{
	/**
	 * @var Filesystem
	 */
	protected $files;

	/**
	 * Translator constructor.
	 * @param Loader     $loader loader
	 * @param string     $locale locale
	 * @param Filesystem $files  files
	 */
	public function __construct(Loader $loader, $locale, Filesystem $files)
	{
		parent::__construct($loader, $locale);
		$this->files = $files;
	}

	/**
	 * Add translation lines to the given locale.
	 * @param array  $lines     line
	 * @param string $locale    locale
	 * @param string $namespace namespace
	 * @return void
	 */
	public function addLines(array $lines, $locale, $namespace = '*')
	{
		foreach ($lines as $key => $value) {
			[$group, $item] = explode('.', $key, 2);

			Arr::set($this->loaded, "$locale.$namespace.$group.$item", $value);
		}
	}

	/**
	 * Fetch all language line from a local.
	 * @param string $local local
	 * @return array
	 */
	public function fetch($local)
	{
		$namespaces = collect($this->loader->namespaces());
		$namespaces->each(function ($path, $namespace) use ($local) {
			if ($this->files->exists($path . DIRECTORY_SEPARATOR . $local)) {
				$groups = collect($this->files->files($path . DIRECTORY_SEPARATOR . $local));
				$groups->each(function ($path) use ($local, $namespace) {
					$this->load($namespace, $this->files->name($path), $local);
				});
			}
		});
		$data = collect();
		collect($this->loaded[$local])->each(function ($value, $key) use ($data) {
			$this->loop($value, $key, $data);
		});

		return $data;
	}

	/**
	 * loop
	 * @param mixed      $data data
	 * @param mixed      $pre  pre
	 * @param Collection $list list
	 */
	private function loop($data, $pre, Collection $list)
	{
		if (is_array($data)) {
			collect($data)->each(function ($data, $key) use ($list, $pre) {
				$pre .= '.' . $key;
				$this->loop($data, $pre, $list);
			});
		}
		else {
			$list->put($pre, $data);
		}
	}

	/**
	 * Retrieve a language line out the loaded array.
	 * @param string $namespace namespace
	 * @param string $group     group
	 * @param string $locale    locale
	 * @param string $item      item
	 * @param array  $replace   replace
	 * @return string|array|null
	 */
	protected function getLine($namespace, $group, $locale, $item, array $replace)
	{
		$this->load($namespace, $group, $locale);

		$line = Arr::get($this->loaded[$locale][$namespace][$group], $item);

		if (is_string($line)) {
			return $this->makeReplacements($line, $replace);
		}

		if (is_array($line) && count($line) > 0) {
			return $line;
		}
	}

	/**
	 * Load the specified language group.
	 * @param string $namespace namespace
	 * @param string $group     group
	 * @param string $locale    locale
	 * @return void
	 */
	public function load($namespace, $group, $locale)
	{
		if ($this->isLoaded($namespace, $group, $locale)) {
			return;
		}
		$lines = $this->loader->load($locale, $group, $namespace);

		$this->loaded[$locale][$namespace][$group] = $lines;
	}

	/**
	 * Determine if the given group has been loaded.
	 * @param string $namespace namespace
	 * @param string $group     group
	 * @param string $locale    locale
	 * @return bool
	 */
	protected function isLoaded($namespace, $group, $locale)
	{
		return isset($this->loaded[$locale][$namespace][$group]);
	}

}
