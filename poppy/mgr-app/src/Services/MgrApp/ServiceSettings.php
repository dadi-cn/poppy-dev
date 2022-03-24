<?php

namespace Poppy\MgrApp\Services\MgrApp;

use Poppy\Core\Services\Contracts\ServiceArray;
use Poppy\MgrApp\Http\Setting\SettingPam;
use Poppy\MgrApp\Http\Setting\SettingSite;
use Poppy\MgrApp\Http\Setting\SettingUpload;

class ServiceSettings implements ServiceArray
{

    public function key(): string
    {
        return 'poppy.system';
    }

    public function data(): array
    {
        return [
            'title' => '系统',
            'forms' => [
                SettingSite::class,
                SettingPam::class,
                SettingUpload::class,
            ],
        ];
    }
}