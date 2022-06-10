<?php

namespace Poppy\MgrPage;


use Illuminate\Console\Scheduling\Schedule;
use Poppy\Framework\Exceptions\ModuleNotFoundException;
use Poppy\Framework\Support\PoppyServiceProvider;
use Poppy\MgrPage\Classes\FormBuilder;
use Poppy\MgrPage\Commands\MixCommand;

/**
 * @property $listens;
 */
class ServiceProvider extends PoppyServiceProvider
{

    /**
     * Bootstrap the module services.
     * @return void
     * @throws ModuleNotFoundException
     */
    public function boot()
    {
        parent::boot('poppy.mgr-page');

        // 注册 api 文档配置
        $this->publishes([
            __DIR__ . '/../resources/views/vendor/pagination-layui.blade.php' => resource_path('views/vendor/pagination/layui.blade.php'),
        ], 'poppy');
        $this->publishes([
            // 需要从项目中反向复制的页面
            __DIR__ . '/../resources/libs/boot/vendor.min.js'      => public_path('assets/libs/boot/vendor.min.js'),
            __DIR__ . '/../resources/libs/boot/poppy.mgr.min.js'   => public_path('assets/libs/boot/poppy.mgr.min.js'),
            __DIR__ . '/../resources/libs/boot/style.css'          => public_path('assets/libs/boot/style.css'),
            // editor
            __DIR__ . '/../resources/libs/boot/wang-editor.min.js' => public_path('assets/libs/boot/wang-editor.min.js'),
            // 编辑器
            __DIR__ . '/../resources/libs/jquery/backstretch'      => public_path('assets/libs/jquery/backstretch'),
            __DIR__ . '/../resources/libs/vue'                     => public_path('assets/libs/vue'),
            __DIR__ . '/../resources/libs/underscore'              => public_path('assets/libs/underscore'),
            __DIR__ . '/../resources/libs/jshash'                  => public_path('assets/libs/jshash'),
            __DIR__ . '/../resources/libs/easy-web/'               => public_path('assets/libs/easy-web/'),
            __DIR__ . '/../resources/libs/jquery/data-tables/'     => public_path('assets/libs/jquery/data-tables/'),
            __DIR__ . '/../resources/libs/layui/'                  => public_path('assets/libs/layui/'),
            __DIR__ . '/../resources/images/'                      => public_path('assets/images/'),
            __DIR__ . '/../resources/font/'                        => public_path('assets/font/'),
        ], 'poppy-mix');
    }

    /**
     * Register the module services.
     * @return void
     */
    public function register()
    {
        $this->app->register(Http\MiddlewareServiceProvider::class);
        $this->app->register(Http\RouteServiceProvider::class);

        $this->registerConsole();

        $this->registerSchedule();

        $this->registerForm();
    }

    public function provides(): array
    {
        return [
            'poppy.mgr-page.form',
        ];
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
            MixCommand::class,
        ]);
    }

    private function registerForm()
    {

        $this->app->singleton('poppy.mgr-page.form', function ($app) {
            $form = new FormBuilder($app['html'], $app['url'], $app['view'], $app['session.store']->token());

            return $form->setSessionStore($app['session.store']);
        });
    }
}