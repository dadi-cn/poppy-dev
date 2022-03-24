<?php

namespace Poppy\MgrApp\Classes\Grid\Tools;

use Illuminate\Support\Fluent;
use Poppy\MgrApp\Classes\Action\Action;
use Poppy\MgrApp\Classes\Contracts\Structable;
use Poppy\MgrApp\Classes\Traits\UseActions;

class Actions implements Structable
{

    use UseActions;

    /**
     *
     */
    public function struct(): array
    {
        $actions = [];
        foreach ($this->items as $append) {
            if ($append instanceof Action) {
                $def       = $append->struct();
                $actions[] = $def;
            }
        }
        return (new Fluent($actions))->toArray();
    }
}
