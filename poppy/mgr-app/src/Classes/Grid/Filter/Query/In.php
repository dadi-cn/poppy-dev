<?php

namespace Poppy\MgrApp\Classes\Grid\Filter\Query;

use Illuminate\Support\Arr;
use Poppy\MgrApp\Classes\Form\Traits\UseOptions;
use Poppy\MgrApp\Classes\Form\Traits\UsePlaceholder;
use Poppy\MgrApp\Classes\Grid\Filter\Traits\AsMultiSelect;

class In extends FilterItem
{
    use UseOptions, AsMultiSelect, UsePlaceholder;

    /**
     * @inheritDoc
     */
    protected string $query = 'whereIn';

    /**
     * Get condition of this filter.
     *
     * @param array $inputs
     * @return mixed
     */
    public function condition(array $inputs)
    {
        $value = Arr::get($inputs, $this->name);

        if (empty($value)) {
            return null;
        }

        $this->value = (array) $value;

        return $this->buildCondition($this->name, $this->value);
    }
}
