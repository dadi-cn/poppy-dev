<?php

namespace Demo\Hooks\MgrDev;

use Demo\Hooks\MgrDev\Checker\CheckerPrefix;
use Poppy\Core\Services\Contracts\ServiceArray;

class Checker implements ServiceArray
{

    public function key(): string
    {
        return 'dev';
    }

    public function data(): array
    {
        return [
            'title'   => 'Demo检测',
            'checker' => [
                CheckerPrefix::class,
            ],
        ];
    }
}