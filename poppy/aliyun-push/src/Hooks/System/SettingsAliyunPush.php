<?php

declare(strict_types=1);

namespace Poppy\AliyunPush\Hooks\System;

use Poppy\AliyunPush\Http\MgrPage\FormSettingAliyunPush;
use Poppy\Core\Services\Contracts\ServiceArray;

class SettingsAliyunPush implements ServiceArray
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
                FormSettingAliyunPush::class,
            ],
        ];
    }
}