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


    /**
     * 自定义查询和字段
     * @param string $url   查询地址
     * @param string $field 查询字段
     * @return $this
     */
    public function query(string $field, string $url = ''): self
    {
        $this->setAttribute('query', $url);
        $this->setAttribute('field', $field);
        return $this;
    }
}
