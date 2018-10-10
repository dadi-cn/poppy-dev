<?php namespace Poppy\Framework\Application;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Poppy\Framework\Agamotto\Agamotto;
use Poppy\Framework\Helper\EnvHelper;

abstract class Controller extends BaseController
{
	use DispatchesJobs, ValidatesRequests;

	/**
	 * @var string 权限(中间件中可以读取, 使用 public 模式)
	 */
	public static $permission;

	/**
	 * @var int
	 */
	protected $pagesize = 15;

	/**
	 * @var string
	 */
	protected $ip;

	/**
	 * @var Agamotto
	 */
	protected $now;

	/**
	 * @var string
	 */
	protected $route;

	public function __construct()
	{
		$this->route = \Route::currentRouteName();
		\View::share([
			'_route' => $this->route,
		]);

		// pagesize
		$this->pagesize = config('poppy.pages.default_size', 15);
		$maxPagesize    = config('poppy.pages.max_size');
		if (\Input::get('pagesize')) {
			$pagesize = abs(intval(\Input::get('pagesize')));
			$pagesize = ($pagesize <= $maxPagesize) ? $pagesize : $maxPagesize;
			if ($pagesize > 0) {
				$this->pagesize = $pagesize;
			}
		}

		$this->ip  = EnvHelper::ip();
		$this->now = Agamotto::now();

		\View::share([
			'_ip'       => $this->ip,
			'_now'      => $this->now,
			'_pagesize' => $this->pagesize,
		]);

		// 自动计算seo
		// 根据路由名称来转换 seo key
		// slt:nav.index  => slt::seo.nav_index
		$seoKey    = str_replace([':', '.'], ['::', '_'], $this->route);
		$enableSeo = config('poppy.seo_enable');
		if ($enableSeo && $seoKey) {
			$seoKey = str_replace('::', '::seo.', $seoKey);
			$this->seo(trans($seoKey));
		}
	}

	protected function seo(...$args)
	{
		$title       = '';
		$description = '';
		if (func_num_args() == 1) {
			$arg = func_get_arg(0);
			if (is_array($arg)) {
				$title       = $arg['title'] ?? '';
				$description = $arg['description'] ?? '';
			}
			if (is_string(func_get_arg(0))) {
				$title       = $arg;
				$description = '';
			}
		}
		elseif (func_num_args() == 2) {
			$title       = func_get_arg(0);
			$description = func_get_arg(1);
		}
		\View::share([
			'_title'       => $title,
			'_description' => $description,
		]);
	}
}

