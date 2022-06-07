<?php

namespace Poppy\System\Classes\Form\Field;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class Checkbox extends MultipleSelect
{
    /**
     * 是否行内显示
     * @var bool
     */
    protected $inline = true;


    /**
     * 默认值
     * @var array
     */
    protected $default = [];

    /**
     * 是否可以全选
     * @var bool
     */
    protected $canCheckAll = false;

    /**
     * @inheritDoc
     */
    public function fill($data)
    {
        $value       = Arr::get($data, $this->column);
        $this->value = is_null($value) ? $this->default : $value;
        $this->value = (array) $this->value;
    }

    /**
     * Set options.
     *
     * @param array|callable|string $options
     *
     * @return $this
     */
    public function options($options = [])
    {
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        if (is_callable($options)) {
            $this->options = $options;
        }
        else {
            $this->options = (array) $options;
        }

        return $this;
    }

    /**
     * 默认值, 当没有数据做填充的时候会取这个默认值(null 值的时候)
     * @param array|callable|string $default
     * @return $this
     */
    public function default($default)
    {
        $this->default = (array) $default;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->addVariables([
            'inline'      => $this->inline,
            'canCheckAll' => $this->canCheckAll,
        ]);
        return parent::render();
    }

    public function skeleton(): array
    {
        return [
            'display'   => $this->inline ? 'inline' : 'stack',
            'check_all' => $this->canCheckAll ? 'Y' : 'N',
        ];
    }

    /**
     * Add a checkbox above this component, so you can select all checkboxes by click on it.
     *
     * @return $this
     */
    public function canCheckAll()
    {
        $this->canCheckAll = true;

        return $this;
    }

    /**
     * Draw inline checkboxes.
     *
     * @return $this
     */
    public function inline()
    {
        $this->inline = true;

        return $this;
    }

    /**
     * Draw stacked checkboxes.
     *
     * @return $this
     */
    public function stacked()
    {
        $this->inline = false;

        return $this;
    }
}
