<?php

declare(strict_types=1);

namespace Poppy\AliyunPush\Hooks\MgrApp;

use Poppy\AliyunPush\Http\MgrApp\SettingAliyunPush;
use Poppy\Core\Services\Contracts\ServiceArray;

class Settings implements ServiceArray
{
    public function key(): string
    {
        return 'poppy.aliyun-push';
    }

    public function data(): array
    {
        return [
            'title' => '阿里云推送',
            'forms' => [
                SettingAliyunPush::class,
            ],
        ];
    }
}