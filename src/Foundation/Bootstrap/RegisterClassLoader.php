<?php namespace Poppy\Framework\Foundation\Bootstrap;

use Poppy\Framework\Classes\ClassLoader;
use Poppy\Framework\Filesystem\Filesystem;
use Poppy\Framework\Foundation\Application;

/**
 * 注册加载器
 */
class RegisterClassLoader
{
	/**
	 * 注册 Loader
	 * @param Application $app
	 */
	public function bootstrap(Application $app): void
	{
		$loader = new ClassLoader(
			new Filesystem(),
			$app->basePath(),
			$app->getCachedClassesPath()
		);

		$app->instance(ClassLoader::class, $loader);

		$loader->register();

		$loader->addDirectories([
			'modules',
		]);

		$app->routeMatched(function () use ($loader) {
			$loader->build();
		});
	}
}
