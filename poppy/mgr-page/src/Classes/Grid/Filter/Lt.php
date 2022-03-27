<?php

namespace Poppy\MgrPage\Classes\Grid\Filter;

use Illuminate\Support\Arr;

class Lt extends AbstractFilter
{
    /**
     * @inheritDoc
     */
    protected $view = 'py-mgr-page::tpl.filter.lt';

    /**
     * Get condition of this filter.
     *
     * @param array $inputs
     *
     * @return array|mixed|void
     */
    public function condition(array $inputs)
    {
        $value = Arr::get($inputs, $this->column);

        if (is_null($value)) {
            return;
        }

        $this->value = $value;

        return $this->buildCondition($this->column, '<=', $this->value);
    }
}
