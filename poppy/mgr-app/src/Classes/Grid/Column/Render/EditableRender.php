<?php

namespace Poppy\MgrApp\Classes\Grid\Column\Render;

use Closure;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Fluent;

class EditableRender extends Render
{

    protected string $type = 'editable';

    /**
     * 自定义编辑地址
     * @param Closure|null $callback
     * @param string $query 自定义编辑地址
     * @param string $field 自定义编辑字段
     * @return Jsonable
     */
    public function render(Closure $callback = null, string $query = '', string $field = ''): Jsonable
    {
        $value = $this->value;
        if ($callback instanceof Closure) {
            $callback = $callback->bindTo($this->row);
            $value    = call_user_func_array($callback, [$this->row]);
        }
        return new Fluent([
            'value' => $value,
            'query' => $query,
            'field' => $field,
        ]);
    }
}
