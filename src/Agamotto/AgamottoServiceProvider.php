<?php namespace Poppy\Framework\Agamotto;

use Illuminate\Support\ServiceProvider as L5ServiceProvider;
use Poppy\Framework\Events\LocaleChanged;

/**
 * 加载器
 * 默认项目是同步加载, 这里取消掉 defer 属性
 */
class AgamottoServiceProvider extends L5ServiceProvider
{

	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$locale = app('translator')->getLocale();
		$this->setAgamottoLocale($locale);

		app('events')->listen(LocaleChanged::class, function (LocaleChanged $event) {
			$this->setAgamottoLocale($event->locale);
		});
	}

	/**
	 * Sets the locale using the correct load order.
	 * 使用指定的加载顺序设置本地化
	 * @param $locale
	 */
	private function setAgamottoLocale($locale)
	{

		Agamotto::setFallbackLocale($this->getFallbackLocale($locale));
		Agamotto::setLocale($locale);
	}

	/**
	 * Split the locale and use it as the fallback.
	 * 拆分 local 并且设置回滚语言
	 * @param $locale
	 * @return bool|string
	 */
	private function getFallbackLocale($locale)
	{
		if ($position = strpos($locale, '-')) {
			$target   = substr($locale, 0, $position);
			$resource = base_path('vendor/jenssegers/date/src/Lang/' . $target . '.php');
			if (file_exists($resource)) {
				return $target;
			}
		}

		return config('app.fallback_locale');
	}
}
