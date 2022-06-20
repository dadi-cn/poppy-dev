<?php

namespace Poppy\MgrApp\Classes\Grid\Column\Render;

use Closure;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Fluent;

/**
 * 将表格设为隐藏模式
 */
class HiddenRender extends Render
{

    protected string $type = 'hidden';

    /**
     * @param Closure|null $callback 展示的内容
     * @param string $query          自定义查询的Url
     * @param string $field          自定义查询字段
     * @return Jsonable
     */
    public function render(Closure $callback = null, string $query = '', string $field = ''): Jsonable
    {
        $value = '';
        if ($callback instanceof Closure) {
            $callback = $callback->bindTo($this->row);
            $value    = call_user_func_array($callback, [$this->row]);
        }
        return new Fluent([
            'query' => $query,
            'field' => $field ?: $this->field,
            'value' => $value,
        ]);
    }
}
