<?php

namespace Poppy\MgrApp\Classes\Filter\Query;

use Poppy\MgrApp\Classes\Filter\Traits\AsText;
use Poppy\MgrApp\Classes\Form\Traits\UsePlaceholder;

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
        $this->defaultValue($inputs);
        if (!$this->value) {
            return null;
        }

        $expr = str_replace('{value}', $this->value, $this->exprFormat);

        return $this->buildCondition($this->name, $this->operator, $expr);
    }
}
