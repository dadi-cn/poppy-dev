<?php namespace Poppy\Framework\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class BladeServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 * @return void
	 */
	public function boot()
	{
	}

	/**
	 * Register the application services.
	 * @return void
	 */
	public function register()
	{
		$this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
			// @poppy($slug)
			$bladeCompiler->directive('poppy', function ($slug) {
				return "<?php if(app('poppy')->exists({$slug}) && app('poppy')->isEnabled({$slug})): ?>";
			});

			$bladeCompiler->directive('endpoppy', function () {
				return '<?php endif; ?>';
			});
		});
	}
}
