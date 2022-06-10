<?php

namespace Poppy\CanalEs;

use Poppy\CanalEs\Commands\MonitorCommand;
use Poppy\CanalEs\Commands\CreateIndexCommand;
use Poppy\CanalEs\Commands\ImportCommand;
use Poppy\Framework\Exceptions\ModuleNotFoundException;
use Poppy\Framework\Support\PoppyServiceProvider as ModuleServiceProviderBase;

class ServiceProvider extends ModuleServiceProviderBase
{

    public function boot()
    {
        parent::boot('poppy.canal-es');
    }

    /**
     * Register the module services.
     * @return void
     */
    public function register()
    {
        $this->commands([
            MonitorCommand::class,
            CreateIndexCommand::class,
            ImportCommand::class,
        ]);
    }
}
