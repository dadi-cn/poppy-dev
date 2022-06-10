<?php

namespace Poppy\Core;

use Illuminate\Console\Scheduling\Schedule;
use Poppy\Core\Listeners\PoppyOptimized\ClearCacheListener;
use Poppy\Framework\Events\PoppyOptimized as PoppyOptimizedEvent;
use Poppy\Framework\Exceptions\ModuleNotFoundException;
use Poppy\Framework\Support\PoppyServiceProvider;

class ServiceProvider extends PoppyServiceProvider
{

    protected array $listens = [
        // poppy
        PoppyOptimizedEvent::class => [
            ClearCacheListener::class,
        ],
    ];

    /**
     * Bootstrap the module services.
     * @return void
     * @throws ModuleNotFoundException
     */
    public function boot()
    {
        parent::boot('poppy.core');

        // 注册 api 文档配置
        $this->publishes([
            __DIR__ . '/../resources/config/doctum-config.php' => storage_path('doctum/config.php'),
        ], 'poppy');
    }

    /**
     * Register the module services.
     * @return void
     */
    public function register()
    {
        // 合并配置
        $this->mergeConfigFrom(__DIR__ . '/../resources/config/core.php', 'poppy.core');

        $this->app->register(Module\ModuleServiceProvider::class);
        $this->app->register(Rbac\RbacServiceProvider::class);

        $this->registerConsole();

        $this->registerSchedule();
    }


    private function registerSchedule()
    {
        app('events')->listen('console.schedule', function (Schedule $schedule) {

        });
    }


    private function registerConsole()
    {
        // system
        $this->commands([
            Commands\PermissionCommand::class,
            Commands\DocCommand::class,
            Commands\OpCommand::class,
            Commands\InspectCommand::class,
            Commands\PersistCommand::class,
        ]);
    }
}