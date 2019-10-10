<?php namespace Poppy\Framework\Support;

use Event;
use Gate;
use Illuminate\Support\ServiceProvider as ServiceProviderBase;
use Poppy\Framework\Agamotto\Agamotto;
use Poppy\Framework\Exceptions\ModuleNotFoundException;

/**
 * PoppyServiceProvider
 */
abstract class PoppyServiceProvider extends ServiceProviderBase
{
	/**
	 * event listener
	 * @var array
	 */
	protected $listens = [];

	/**
	 * policy
	 * @var array
	 */
	protected $policies = [];

	/**
	 * Bootstrap the application events.
	 * @return void
	 * @throws ModuleNotFoundException
	 */
	public function boot()
	{
		if ($module = $this->getModule(func_get_args())) {
			/*
			 * Register paths for: config, translator, view
			 */
			$modulePath = poppy_path($module);
			$this->loadViewsFrom($modulePath . '/resources/views', $module);
			$this->loadTranslationsFrom($modulePath . '/resources/lang', $module);
			$this->loadMigrationsFrom($modulePath . '/resources/database/migrations');

			if ($this->listens) {
				$this->bootListener();
			}

			if ($this->policies) {
				$this->bootPolicies();
			}
		}
	}

	/**
	 * @param $args
	 * @return null
	 * @throws ModuleNotFoundException
	 */
	public function getModule($args)
	{
		$slug = (isset($args[0]) and is_string($args[0])) ? $args[0] : null;
		if ($slug) {
			$module = app('poppy')->where('slug', $slug);
			if (is_null($module)) {
				throw new ModuleNotFoundException($slug);
			}

			return $slug;
		}

		return null;
	}

	/**
	 * Registers a new console (artisan) command
	 * @param string $key   The command name
	 * @param string $class The command class
	 * @return void
	 */
	public function registerConsoleCommand($key, $class)
	{
		$key = 'command.poppy.' . $key;
		$this->app->singleton($key, function ($app) use ($class) {
			return new $class();
		});
		$this->commands($key);
	}

	/**
	 * 注册系统中用到的策略
	 */
	protected function bootPolicies()
	{
		foreach ($this->policies as $key => $value) {
			Gate::policy($key, $value);
		}
	}

	/**
	 * 监听核心事件
	 */
	protected function bootListener()
	{
		foreach ($this->listens as $event => $listeners) {
			foreach ($listeners as $listener) {
				Event::listen($event, $listener);
			}
		}
	}

	/**
	 * consoleLog
	 * @return string
	 */
	protected function consoleLog()
	{
		$day = Agamotto::now()->toDateString();

		return storage_path('logs/console-' . $day . '.log');
	}
}