<?php

namespace Poppy\Framework\Support;

use Carbon\Carbon;
use Event;
use Gate;
use Illuminate\Support\ServiceProvider as ServiceProviderBase;
use Illuminate\Support\Str;
use Poppy\Framework\Classes\Traits\MigrationTrait;
use Poppy\Framework\Exceptions\ModuleNotFoundException;

/**
 * PoppyServiceProvider
 */
abstract class PoppyServiceProvider extends ServiceProviderBase
{

    use MigrationTrait;

    /**
     * event listener
     * @var array
     */
    protected array $listens = [];

    /**
     * policy
     * @var array
     */
    protected array $policies = [];

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

            if (Str::start($module, 'poppy')) {
                // 模块命名 poppy.mgr-page
                // namespace : py-mgr-page
                // 命名空间进行简化处理
                $namespace = str_replace('poppy.', 'py-', $module);
            }
            else {
                // 模块命名 module.order
                // namespace : order
                $namespace = Str::after($module, '.');
            }

            $this->loadViewsFrom($modulePath . '/resources/views', $namespace);
            $this->loadTranslationsFrom($modulePath . '/resources/lang', $namespace);
            $this->loadMigrationsFrom($this->getMigrationPath($module));

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
    protected function consoleLog(): string
    {
        $day = Carbon::now()->toDateString();
        return storage_path('logs/console-' . $day . '.log');
    }
}