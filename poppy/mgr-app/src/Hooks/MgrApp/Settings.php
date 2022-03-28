<?php

namespace Poppy\MgrApp\Hooks\MgrApp;

use Poppy\Core\Services\Contracts\ServiceArray;
use Poppy\MgrApp\Http\Setting\SettingPam;
use Poppy\MgrApp\Http\Setting\SettingSite;
use Poppy\MgrApp\Http\Setting\SettingUpload;

class Settings implements ServiceArray
{

    public function key(): string
    {
        return 'poppy.mgr-app';
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