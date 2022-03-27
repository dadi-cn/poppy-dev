<?php

namespace Poppy\Area\Classes\Grid\Filter\Presenter;

use Poppy\Area\Models\SysArea;
use Poppy\MgrPage\Classes\Grid\Filter\Presenter\Presenter;

class Area extends Presenter
{
    public function view(): string
    {
        return 'py-area::tpl.filter.area';
    }

    public function variables(): array
    {
        return [
            'area' => SysArea::cityTree(),
        ];
    }
}
