<?php

namespace Poppy\MgrApp\Classes\Grid\Filter\Query;

use Illuminate\Support\Arr;
use Poppy\MgrApp\Classes\Form\Traits\UseOptions;
use Poppy\MgrApp\Classes\Form\Traits\UsePlaceholder;
use Poppy\MgrApp\Classes\Grid\Filter\Traits\AsSelect;
use Poppy\MgrApp\Classes\Grid\Filter\Traits\AsText;

class NotEqual extends FilterItem
{
    use AsSelect, AsText,
        UsePlaceholder,
        UseOptions;
    /**
     * @inheritDoc
     */
    public function condition(array $inputs)
    {
        $value = Arr::get($inputs, $this->name);

        if (!isset($value)) {
            return;
        }

        $this->value = $value;

        return $this->buildCondition($this->name, '!=', $this->value);
    }
}
