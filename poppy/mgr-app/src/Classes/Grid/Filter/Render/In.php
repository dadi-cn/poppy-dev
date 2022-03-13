<?php

namespace Poppy\MgrApp\Classes\Grid\Filter\Render;

use Illuminate\Support\Arr;

class In extends AbstractFilterItem
{
    /**
     * @inheritDoc
     */
    protected string $query = 'whereIn';

    /**
     * Get condition of this filter.
     *
     * @param array $inputs
     *
     * @return mixed
     */
    public function condition(array $inputs)
    {
        $value = Arr::get($inputs, $this->column);

        if (is_null($value)) {
            return null;
        }

        $this->value = (array) $value;

        return $this->buildCondition($this->column, $this->value);
    }
}