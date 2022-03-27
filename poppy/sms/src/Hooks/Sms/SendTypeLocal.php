<?php

namespace Poppy\Sms\Hooks\Sms;

use Poppy\Core\Services\Contracts\ServiceArray;
use Poppy\Sms\Classes\LocalSmsProvider;

class SendTypeLocal implements ServiceArray
{

    public function key(): string
    {
        return 'local';
    }

    public function data():array
    {
        return [
            'title'    => '本地',
            'provider' => LocalSmsProvider::class,
        ];
    }
}