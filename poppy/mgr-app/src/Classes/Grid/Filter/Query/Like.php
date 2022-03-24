<?php

namespace Poppy\MgrApp\Classes\Grid\Filter\Query;

use Illuminate\Support\Arr;
use Poppy\MgrApp\Classes\Form\Traits\UsePlaceholder;
use Poppy\MgrApp\Classes\Grid\Filter\Traits\AsText;

class Like extends FilterItem
{
    use AsText, UsePlaceholder;

    /**
     * @var string
     */
    protected string $exprFormat = '%{value}%';

    /**
     * @var string
     */
    protected string $operator = 'like';

    /**
     * Get condition of this filter.
     *
     * @param array $inputs
     *
     * @return array|mixed|void
     */
    public function condition(array $inputs)
    {
        $value = Arr::get($inputs, $this->name);

        if (empty($value)) {
            return;
        }

        $this->value = $value;

        $expr = str_replace('{value}', $this->value, $this->exprFormat);

        return $this->buildCondition($this->name, $this->operator, $expr);
    }
}
