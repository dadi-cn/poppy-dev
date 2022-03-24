<?php

declare(strict_types=1);

namespace Poppy\AliyunPush\Services\MgrApp;

use Poppy\AliyunPush\Http\Setting\SettingAliyunPush;
use Poppy\Core\Services\Contracts\ServiceArray;

class ServiceSettings implements ServiceArray
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