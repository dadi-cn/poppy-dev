<?php

namespace Poppy\MgrApp\Classes\Filter\Query;

use Illuminate\Support\Arr;
use Poppy\MgrApp\Classes\Filter\Traits\AsSelect;
use Poppy\MgrApp\Classes\Filter\Traits\AsText;
use Poppy\MgrApp\Classes\Form\Traits\UseOptions;
use Poppy\MgrApp\Classes\Form\Traits\UsePlaceholder;

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
        $this->defaultValue($inputs);
        if (!$this->value) {
            return null;
        }

        return $this->buildCondition($this->name, '!=', $this->value);
    }
}
