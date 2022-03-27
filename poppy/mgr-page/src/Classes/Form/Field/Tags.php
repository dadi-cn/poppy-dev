<?php

namespace Poppy\MgrPage\Classes\Form\Field;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Poppy\MgrPage\Classes\Form\Field;
use function collect;

class Tags extends Field
{
    /**
     * @var array
     */
    protected $value = [];

    /**
     * @var string
     */
    protected $visibleColumn = null;

    /**
     * @var string
     */
    protected $key = null;

    /**
     * @var Closure
     */
    protected $saveAction = null;


    protected $max = 1;

    /**
     * @inheritDoc
     */
    public function fill($data)
    {
        $this->value = Arr::get($data, $this->column);

        if (is_array($this->value)) {
            $this->value = array_column($this->value, $this->visibleColumn, $this->key);
        }

        if (is_string($this->value)) {
            $this->value = explode(',', $this->value);
        }

        $this->value = array_filter((array) $this->value, 'strlen');
    }

    /**
     * Set the field options.
     *
     * @param array|Collection|Arrayable $options
     *
     * @return $this|Field
     */
    public function options($options = [])
    {
        if ($options instanceof Collection) {
            $options = $options->toArray();
        }
        $this->options = $options + $this->options;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function prepare($value)
    {
        $value = array_filter($value, 'strlen');

        if (is_array($value) && !Arr::isAssoc($value)) {
            $value = implode(',', $value);
        }

        return $value;
    }

    /**
     * Get or set value for this field.
     *
     * @param mixed $value
     *
     * @return $this|array|mixed
     */
    public function value($value = null)
    {
        if (is_null($value)) {
            return empty($this->value) ? ($this->getDefault() ?? []) : $this->value;
        }

        $this->value = (array) $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->addVariables([
            'options' => $this->options,
        ]);

        return parent::render();
    }


    public function skeleton(): array
    {
        $options = [];
        collect($this->options)->map(function ($value, $id) use (&$options) {
            $options[] = [
                'id'    => $id,
                'value' => $value,
            ];
        })->toArray();
        return [
            'options' => $options,
        ];
    }

    /**
     * Set visible column and key of data.
     *
     * @param $visibleColumn
     * @param $key
     *
     * @return $this
     */
    public function pluck($visibleColumn, $key)
    {
        $this->visibleColumn = $visibleColumn;
        $this->key           = $key;

        return $this;
    }

    /**
     * Set save Action.
     *
     * @param Closure $saveAction
     *
     * @return $this
     */
    public function saving(Closure $saveAction)
    {
        $this->saveAction = $saveAction;

        return $this;
    }
}
