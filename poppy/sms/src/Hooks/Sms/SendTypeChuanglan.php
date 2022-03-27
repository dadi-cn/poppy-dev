<?php

namespace Poppy\Sms\Hooks\Sms;

use Poppy\Core\Services\Contracts\ServiceArray;
use Poppy\Sms\Classes\ChuanglanSmsProvider;
use Poppy\Sms\Http\MgrPage\FormSettingChuanglan;

class SendTypeChuanglan implements ServiceArray
{

    public function key(): string
    {
        return 'chuanglan';
    }

    public function data():array
    {
        return [
            'title'    => '创蓝',
            'provider' => ChuanglanSmsProvider::class,
            'setting'  => FormSettingChuanglan::class,
            'route'    => 'py-sms:backend.store.chuanglan',
        ];
    }
}