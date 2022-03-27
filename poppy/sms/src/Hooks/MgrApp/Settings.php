<?php

namespace Poppy\Sms\Hooks\MgrApp;

use Poppy\Core\Services\Contracts\ServiceArray;
use Poppy\Sms\Http\MgrApp\SettingSms;

class Settings implements ServiceArray
{

    public function key(): string
    {
        return 'poppy.sms';
    }

    public function data(): array
    {
        return [
            'title' => '短信配置',
            'forms' => [
                SettingSms::class,
            ],
        ];
    }
}