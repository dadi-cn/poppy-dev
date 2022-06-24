<?php

namespace Poppy\MgrApp\Classes\Table\Option;

abstract class QueryOption extends Option
{

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
