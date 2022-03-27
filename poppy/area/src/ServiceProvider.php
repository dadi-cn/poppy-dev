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
    protected $listens = [
        PoppyOptimized::class => [
            Listeners\PoppyOptimized\ClearCacheListener::class,
        ],
    ];
    /**
     * @var string the poppy name slug
     */
    private $name = 'poppy.area';

    /**
     * Bootstrap the module services.
     * @return void
     * @throws ModuleNotFoundException
     */
    public function boot()
    {
        parent::boot($this->name);
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
