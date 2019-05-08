<?php namespace Poppy\Framework;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\Framework\Helper\UtilHelper;

class FrameworkServiceProvider extends ServiceProvider
{
	use PoppyTrait;

	/**
	 * Bootstrap the application events.
	 * @return void
	 */
	public function boot()
	{
		// 注册 api 文档配置
		$this->publishes([
			__DIR__ . '/../config/poppy.php' => config_path('poppy.php'),
		], 'poppy-framework');

		$this->app['poppy']->register();

		// 定义视图
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'poppy');
		$this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'poppy');

		$this->bootValidation();

		// Carbon
		Carbon::setLocale(config('app.locale'));
	}

	/**
	 * Register the service provider.
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfigFrom(
			__DIR__ . '/../config/poppy.php',
			'poppy'
		);

		$this->app->register(Agamotto\AgamottoServiceProvider::class);
		$this->app->register(Console\ConsoleServiceProvider::class);
		$this->app->register(Console\GeneratorServiceProvider::class);
		$this->app->register(Providers\BladeServiceProvider::class);
		$this->app->register(Poppy\PoppyServiceProvider::class);
		$this->app->register(Parse\ParseServiceProvider::class);
		$this->app->register(Translation\TranslationServiceProvider::class);
		$this->app->register(Update\UpdateServiceProvider::class);
	}

	/**
	 * @return array
	 * @throws Exceptions\ModuleNotFoundException
	 */
	protected function providerFiles(): array
	{
		$modules = app()->make('poppy')->all();
		$files   = [];

		foreach ($modules as $module) {
			$serviceProvider = poppy_class($module['slug'], 'ServiceProvider');
			if (class_exists($serviceProvider)) {
				$files[] = $serviceProvider;
			}
		}

		return $files;
	}

	private function bootValidation()
	{
		$this->getValidation()->extend('mobile', function ($attribute, $value, $parameters) {
			return UtilHelper::isMobile($value);
		});
		$this->getValidation()->extend('json', function ($attribute, $value, $parameters) {
			return UtilHelper::isJson($value);
		});
		$this->getValidation()->extend('date', function ($attribute, $value, $parameters) {
			return UtilHelper::isDate($value);
		});
		$this->getValidation()->extend('chid', function ($attribute, $value, $parameters) {
			return UtilHelper::isChId($value);
		});
		$this->getValidation()->extend('password', function ($attribute, $value, $parameters) {
			return UtilHelper::isPwd($value);
		});
	}

	/**
	 * Get the services provided by the provider.
	 * @return array
	 */
	public function provides()
	{
		return [];
	}
}