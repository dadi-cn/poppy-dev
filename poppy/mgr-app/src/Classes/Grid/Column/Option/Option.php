<?php

namespace Poppy\MgrApp\Classes\Grid\Column\Option;

use Poppy\MgrApp\Classes\Contracts\Structable;
use Poppy\MgrApp\Classes\Form\Traits\UseFieldAttr;

abstract class Option implements Structable
{

    use UseFieldAttr;

    public function __construct()
    {
        $this->fieldAttrInit();
    }


    /**
     * 返回结构化数据
     * @return object
     */
    public function struct(): object
    {
        return $this->fieldAttr();
    }
}
