<?php

namespace Poppy\Sms\Hooks\Sms;

use Poppy\Core\Services\Contracts\ServiceArray;
use Poppy\Sms\Classes\AliyunSmsProvider;
use Poppy\Sms\Http\MgrPage\FormSettingAliyun;

class SendTypeAliyun implements ServiceArray
{

    public function key(): string
    {
        return 'aliyun';
    }

    public function data():array
    {
        return [
            'title'    => '阿里云',
            'provider' => AliyunSmsProvider::class,
            'setting'  => FormSettingAliyun::class,
            'route'    => 'py-sms:backend.store.aliyun',
        ];
    }
}