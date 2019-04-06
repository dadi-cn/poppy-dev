<?php namespace Poppy\Framework\Classes\Traits;

use Illuminate\Auth\AuthManager;
use Illuminate\Cache\TaggableStore;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\DatabaseManager;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailer;
use Illuminate\Redis\RedisManager;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Session\SessionManager;
use Illuminate\View\Factory;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\MountManager;
use Poppy\Framework\Foundation\Application;
use Poppy\Framework\GraphQL\GraphQL;
use Poppy\Framework\Poppy\Poppy;
use Poppy\Framework\Translation\Translator;
use Psr\Log\LoggerInterface;

trait PoppyTrait
{
	/**
	 * @return AuthManager
	 */
	protected function getAuth(): AuthManager
	{
		return $this->getContainer()->make('auth');
	}

	/**
	 * @return Translator
	 */
	protected function getTranslator(): Translator
	{
		return $this->getContainer()->make('translator');
	}

	/**
	 * Get configuration instance.
	 * @return Repository
	 */
	protected function getConfig()
	{
		return $this->getContainer()->make('config');
	}

	/**
	 * @return DatabaseManager
	 */
	protected function getDb(): DatabaseManager
	{
		return $this->getContainer()->make('db');
	}

	/**
	 * Get console instance.
	 * @return Kernel
	 */
	protected function getConsole()
	{
		$kernel = $this->getContainer()->make(Kernel::class);
		$kernel->bootstrap();

		return $kernel->getArtisan();
	}

	/**
	 * Get IoC Container.
	 * @return Container | Application
	 */
	protected function getContainer(): Container
	{
		return Container::getInstance();
	}

	/**
	 * Get mailer instance.
	 * @return Mailer
	 */
	protected function getMailer(): Mailer
	{
		return $this->getContainer()->make('mailer');
	}

	/**
	 * Get session instance.
	 * @return SessionManager
	 */
	protected function getSession(): SessionManager
	{
		return $this->getContainer()->make('session');
	}

	/**
	 * @return Request
	 */
	protected function getRequest(): Request
	{
		return $this->getContainer()->make('request');
	}

	/**
	 * @return Redirector
	 */
	protected function getRedirector(): Redirector
	{
		return $this->getContainer()->make('redirect');
	}

	/**
	 * @return \Illuminate\Validation\Factory
	 */
	protected function getValidation(): \Illuminate\Validation\Factory
	{
		return $this->getContainer()->make('validator');
	}

	/**
	 * @return Dispatcher
	 */
	protected function getEvent(): Dispatcher
	{
		return $this->getContainer()->make('events');
	}

	/**
	 * @return LoggerInterface
	 */
	protected function getLogger(): LoggerInterface
	{
		return $this->getContainer()->make('log');
	}

	/**
	 * @return ResponseFactory
	 */
	protected function getResponse()
	{
		return $this->getContainer()->make(ResponseFactory::class);
	}

	/**
	 * @return Filesystem
	 */
	protected function getFile()
	{
		return $this->getContainer()->make('files');
	}

	/**
	 * @return UrlGenerator
	 */
	protected function getUrl()
	{
		return $this->getContainer()->make('url');
	}

	/**
	 * @param string $tag
	 * @return mixed
	 */
	protected function getCache($tag = '')
	{
		$cache = $this->getContainer()->make('cache');
		if ($tag && $cache->getStore() instanceof TaggableStore) {
			return $cache->tags($tag);
		}

		return $cache;
	}

	/**
	 * @return RedisManager
	 */
	protected function getRedis(): RedisManager
	{
		return $this->getContainer()->make('redis');
	}

	/**
	 * @return Poppy
	 */
	protected function getPoppy(): Poppy
	{
		return $this->getContainer()->make('poppy');
	}

	/**
	 * @return Factory
	 */
	protected function getView(): Factory
	{
		return $this->getContainer()->make('view');
	}

	/**
	 * Publish the file to the given path.
	 * @param string $from
	 * @param string $to
	 */
	protected function publishFile($from, $to)
	{
		$this->createParentDirectory(dirname($to));
		$this->getFile()->copy($from, $to);
	}

	/**
	 * Create the directory to house the published files if needed.
	 * @param $directory
	 */
	protected function createParentDirectory($directory)
	{
		if (!$this->getFile()->isDirectory($directory)) {
			$this->getFile()->makeDirectory($directory, 0755, true);
		}
	}

	/**
	 * Publish the directory to the given directory.
	 * @param $from
	 * @param $to
	 */
	protected function publishDirectory($from, $to)
	{
		$manager = new MountManager([
			'from' => new Flysystem(new LocalAdapter($from)),
			'to'   => new Flysystem(new LocalAdapter($to)),
		]);
		foreach ($manager->listContents('from://', true) as $file) {
			if ($file['type'] === 'file') {
				$manager->put('to://' . $file['path'], $manager->read('from://' . $file['path']));
			}
		}
	}
}

