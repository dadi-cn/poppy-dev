<?php

namespace Demo\Hooks\MgrDev\Checker;

use Poppy\MgrApp\Classes\Contracts\Checkable;

class CheckerPrefix implements Checkable
{
    public function check(): array
    {
        $errors = [];
        if (sys_setting('system::site.description') !== 'Poppy') {
            $errors[] = [
                'title' => 'Demo 测试, 描述必须是 Poppy',
                'type'  => 'warning'
            ];
        }
        return $errors;
    }
}