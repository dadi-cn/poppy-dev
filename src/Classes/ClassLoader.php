<?php namespace Poppy\Framework\Classes;

use Exception;
use Poppy\Framework\Filesystem\Filesystem;
use Throwable;

/**
 * Class loader
 * 一个简单的文件加载器, 加载的是小写的文件夹名称和首字母驼峰模式的类名
 */
class ClassLoader
{
	/**
	 * 文件实例
	 * @var Filesystem
	 */
	public $files;

	/**
	 * 基本路径.
	 * @var string
	 */
	public $basePath;

	/**
	 * manifest 路径.
	 * @var string|null
	 */
	public $manifestPath;

	/**
	 * 加载 manifest 数组局.
	 * @var array
	 */
	public $manifest;

	/**
	 * 是否要重写 manifest.
	 * @var bool
	 */
	protected $manifestDirty = false;

	/**
	 * 注册的目录
	 * @var array
	 */
	protected $directories = [];

	/**
	 * 确定一个 ClassLoader 是否已经注册
	 * @var bool
	 */
	protected $registered = false;

	/**
	 * Create a new package manifest instance.
	 * @param  Filesystem $files
	 * @param  string     $basePath
	 * @param  string     $manifestPath
	 */
	public function __construct(Filesystem $files, $basePath, $manifestPath)
	{
		$this->files        = $files;
		$this->basePath     = $basePath;
		$this->manifestPath = $manifestPath;
	}

	/**
	 * 加载指定文件.
	 * @param  string $class
	 * @return bool||void
	 */
	public function load($class)
	{
		if (
			isset($this->manifest[$class]) &&
			$this->isRealFilePath($path = $this->manifest[$class])
		) {
			require_once $this->basePath . DIRECTORY_SEPARATOR . $path;

			return true;
		}

		list($lowerPath, $upperClass) = static::normalizeClass($class);

		foreach ($this->directories as $directory) {
			if ($this->isRealFilePath($path = $directory . DIRECTORY_SEPARATOR . $lowerPath)) {
				$this->includeClass($class, $path);

				return true;
			}

			if ($this->isRealFilePath($path = $directory . DIRECTORY_SEPARATOR . $upperClass)) {
				$this->includeClass($class, $path);

				return true;
			}
		}

		return false;
	}

	/**
	 * 检测给定相对路径是否是存在的文件
	 * @param  string $path
	 * @return bool
	 */
	protected function isRealFilePath($path)
	{
		return is_file(realpath($this->basePath . DIRECTORY_SEPARATOR . $path));
	}

	/**
	 * 包含一个类并且添加到 manifest 中
	 * @param  string $class
	 * @param  string $path
	 * @return void
	 */
	protected function includeClass($class, $path)
	{
		require_once $this->basePath . DIRECTORY_SEPARATOR . $path;

		$this->manifest[$class] = $path;

		$this->manifestDirty = true;
	}

	/**
	 * 注册加载器
	 * @return void
	 */
	public function register()
	{
		if ($this->registered) {
			return;
		}

		$this->ensureManifestIsLoaded();

		$this->registered = spl_autoload_register([$this, 'load']);
	}

	/**
	 * 创建清单并且写到磁盘
	 * @return void
	 * @throws Exception
	 */
	public function build()
	{
		if (!$this->manifestDirty) {
			return;
		}

		$this->write($this->manifest);
	}

	/**
	 * 添加目录
	 * @param  string|array $directories
	 * @return void
	 */
	public function addDirectories($directories)
	{
		$this->directories = array_merge($this->directories, (array) $directories);

		$this->directories = array_unique($this->directories);
	}

	/**
	 * 移除目录
	 * @param  string|array $directories
	 * @return void
	 */
	public function removeDirectories($directories = null)
	{
		if (is_null($directories)) {
			$this->directories = [];
		}
		else {
			$directories = (array) $directories;

			$this->directories = array_filter($this->directories, function ($directory) use ($directories) {
				return !in_array($directory, $directories);
			});
		}
	}

	/**
	 * 获取注册的目录
	 * @return array
	 */
	public function getDirectories()
	{
		return $this->directories;
	}

	/**
	 * 从类名获取文件名
	 * @param  string $class
	 * @return array
	 */
	protected function normalizeClass($class)
	{
		/*
		 * Strip first slash
		 */
		if ($class[0] == '\\') {
			$class = substr($class, 1);
		}

		/*
		 * Lowercase folders
		 */
		$parts     = explode('\\', $class);
		$file      = array_pop($parts);
		$poppyName = array_shift($parts);
		$namespace = implode('\\', $parts);
		$directory = str_replace(['\\', '_'], DIRECTORY_SEPARATOR, $namespace);

		// Socialite/QqMobile
		$directories = explode(DIRECTORY_SEPARATOR, $directory);

		// socialite/qq_web
		$directory = array_reduce($directories, function ($carry, $directory) {
			if ($carry) {
				$carry .= DIRECTORY_SEPARATOR;
			}

			return $carry . snake_case($directory);
		});

		/*
		 * Provide both alternatives
		 */
		$lowerPath  = strtolower($poppyName) . DIRECTORY_SEPARATOR .
			'src' . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . $file . '.php';
		$upperClass = $directory . DIRECTORY_SEPARATOR . $file . '.php';

		return [$lowerPath, $upperClass];
	}

	/**
	 * 确保清单已经加载进内存
	 */
	protected function ensureManifestIsLoaded()
	{
		if (!is_null($this->manifest)) {
			return;
		}

		if (file_exists($this->manifestPath)) {
			try {
				$this->manifest = $this->files->getRequire($this->manifestPath);

				if (!is_array($this->manifest)) {
					$this->manifest = [];
				}
			} catch (Throwable $ex) {
				$this->manifest = [];
			}
		}
		else {
			$this->manifest = [];
		}
	}

	/**
	 * 清单写入在磁盘
	 * @param  array $manifest
	 * @throws \Exception
	 */
	protected function write(array $manifest)
	{
		if (!is_writable(dirname($this->manifestPath))) {
			throw new Exception('make sure `storage/framework/cache` exists and writable ?');
		}

		$this->files->put(
			$this->manifestPath,
			'<?php return ' . var_export($manifest, true) . ';'
		);
	}
}

