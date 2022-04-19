<?php

namespace Poppy\MgrApp\Hooks\MgrApp;

use Poppy\Core\Services\Contracts\ServiceArray;
use Poppy\MgrApp\Http\MgrApp\SettingMail;
use Poppy\MgrApp\Http\MgrApp\SettingPam;
use Poppy\MgrApp\Http\MgrApp\SettingSite;
use Poppy\MgrApp\Http\MgrApp\SettingUpload;

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
                SettingMail::class
            ],
        ];
    }
}