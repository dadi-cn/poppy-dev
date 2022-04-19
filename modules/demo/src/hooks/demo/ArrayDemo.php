<?php

namespace Demo\Hooks\Demo;

use Poppy\Core\Services\Contracts\ServiceArray;

/**
 * 选择广告位
 */
class ArrayDemo implements ServiceArray
{

    public function key(): string
    {
        return 'poppy-core-array-service';
    }


    public function data(): array
    {
        return [];
    }
}