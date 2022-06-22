<?php

namespace Poppy\MgrApp\Classes\Grid\Column\Render;

use Closure;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Fluent;
use Poppy\MgrApp\Classes\Form\Traits\UseFieldAttr;
use Poppy\MgrApp\Classes\Form\Traits\UseOptions;
use Poppy\MgrApp\Classes\Form\Traits\UsePlaceholder;
use Poppy\MgrApp\Classes\Grid\Filter\Traits\AsSelect;

final class SelectRender extends Render
{

    use AsSelect, UseFieldAttr, UseOptions, UsePlaceholder;

    protected string $type = 'select';

    public function __construct($value, $row, $field)
    {
        parent::__construct($value, $row, $field);
        $this->fieldAttrInit();
    }

    /**
     * 自定义编辑地址
     * @param array $asSelect
     * @param Closure|null $callback
     * @param string $query 自定义编辑地址
     * @param string $field 自定义编辑字段
     * @return Jsonable
     */
    public function render(array $asSelect = [], Closure $callback = null, string $query = '', string $field = ''): Jsonable
    {
        $options     = data_get($asSelect, 'options', []);
        $placeholder = data_get($asSelect, 'placeholder', '');
        $filterable  = data_get($asSelect, 'filterable', false);
        $this->asSelect($options, $placeholder, $filterable);

        $value = $this->value;
        if ($callback instanceof Closure) {
            $callback = $callback->bindTo($this->row);
            $value    = call_user_func_array($callback, [$this->row]);
        }
        return new Fluent([
            'value' => $value,
            'query' => $query,
            'field' => $field,
            'attr'  => $this->fieldAttr(),
        ]);
    }
}
