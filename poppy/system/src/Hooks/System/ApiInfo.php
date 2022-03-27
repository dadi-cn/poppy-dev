<?php

namespace Poppy\System\Hooks\System;

use Poppy\Core\Services\Contracts\ServiceArray;
use function sys_setting;

class ApiInfo implements ServiceArray
{

    public function key(): string
    {
        return 'py-system';
    }

    public function data(): array
    {
        return [
            'title' => sys_setting('py-system::site.name'),
            'logo'  => sys_setting('py-system::site.logo'),
        ];
    }
}