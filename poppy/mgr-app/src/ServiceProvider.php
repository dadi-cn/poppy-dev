<?php

namespace Poppy\MgrApp;

use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\Framework\Exceptions\ModuleNotFoundException;
use Poppy\Framework\Support\PoppyServiceProvider;
use Poppy\MgrApp\Classes\Form\FieldDef;

/**
 * @property $listens;
 */
class ServiceProvider extends PoppyServiceProvider
{
    use PoppyTrait;

    /**
     * Bootstrap the module services.
     * @return void
     * @throws ModuleNotFoundException
     */
    public function boot()
    {
        parent::boot('poppy.mgr-app');

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

        $this->registerForm();
    }

    public function provides(): array
    {
        return [];
    }

    private function registerForm()
    {
        FieldDef::registerDependencies();
    }

    private function bootConfigs()
    {
    }
}