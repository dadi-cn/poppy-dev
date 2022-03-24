<?php

namespace Poppy\MgrApp;

use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\Framework\Exceptions\ModuleNotFoundException;
use Poppy\Framework\Support\PoppyServiceProvider;

/**
 * @property $listens;
 */
class ServiceProvider extends PoppyServiceProvider
{
    use PoppyTrait;

    /**
     * @var string Module name
     */
    protected $name = 'poppy.mgr-app';

    protected $listens = [
    ];

    protected $policies = [

    ];

    /**
     * Bootstrap the module services.
     * @return void
     * @throws ModuleNotFoundException
     */
    public function boot()
    {
        parent::boot($this->name);

        $this->bootConfigs();
    }

    /**
     * Register the module services.
     * @return void
     */
    public function register()
    {
        $this->app->register(Http\MiddlewareServiceProvider::class);
        $this->app->register(Http\RouteServiceProvider::class);
    }

    public function provides(): array
    {
        return [];
    }

    private function registerSchedule()
    {

    }

    /**
     * register rbac and alias
     */
    private function registerContracts()
    {


    }

    private function registerConsole()
    {
        $this->commands([
        ]);
    }

    private function registerAuth()
    {

    }

    private function bootConfigs()
    {
    }
}