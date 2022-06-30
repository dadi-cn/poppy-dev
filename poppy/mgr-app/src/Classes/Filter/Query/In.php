<?php

namespace Poppy\MgrApp\Classes\Filter\Query;

use Illuminate\Support\Arr;
use Poppy\MgrApp\Classes\Filter\Traits\AsMultiSelect;
use Poppy\MgrApp\Classes\Form\Traits\UseOptions;
use Poppy\MgrApp\Classes\Form\Traits\UsePlaceholder;

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
        return $this->buildCondition($this->name, (array) $this->value);
    }
}
