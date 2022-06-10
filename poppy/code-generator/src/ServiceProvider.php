<?php

declare(strict_types=1);

namespace Poppy\CodeGenerator;

use Poppy\CodeGenerator\Commands\SettingGenerateCommand;
use Poppy\Framework\Support\PoppyServiceProvider;

class ServiceProvider extends PoppyServiceProvider
{

    public function boot()
    {
        parent::boot('poppy.code-generator');
    }

    public function register()
    {
        $this->registerCommand();
    }

    private function registerCommand()
    {
        $this->commands([
            SettingGenerateCommand::class,
        ]);
    }
}