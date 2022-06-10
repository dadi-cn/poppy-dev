<?php

namespace Poppy\Area;

use Poppy\Area\Commands\InitCommand;
use Poppy\Area\Http\RouteServiceProvider;
use Poppy\Framework\Events\PoppyOptimized;
use Poppy\Framework\Exceptions\ModuleNotFoundException;
use Poppy\Framework\Support\PoppyServiceProvider as ModuleServiceProviderBase;
use Poppy\MgrPage\Classes\Form;
use Poppy\MgrPage\Classes\Grid\Filter;

class ServiceProvider extends ModuleServiceProviderBase
{
    protected array $listens = [
        PoppyOptimized::class => [
            Listeners\PoppyOptimized\ClearCacheListener::class,
        ],
    ];

    /**
     * Bootstrap the module services.
     * @return void
     * @throws ModuleNotFoundException
     */
    public function boot()
    {
        parent::boot('poppy.area');
    }

    /**
     * Register the module services.
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->commands([
            InitCommand::class,
        ]);

        Form::extend('area', Classes\Form\Field\Area::class);
        Filter::extend('area', Classes\Grid\Filter\Area::class);
    }
}
