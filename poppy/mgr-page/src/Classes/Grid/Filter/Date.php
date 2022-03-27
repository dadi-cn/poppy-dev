<?php

namespace Poppy\MgrPage\Classes\Grid\Filter;

class Date extends AbstractFilter
{
    /**
     * @inheritDoc
     */
    protected $query = 'whereDate';

    /**
     * @var string
     */
    protected $fieldName = 'date';

    /**
     * @inheritDoc
     */
    public function __construct($column, $label = '')
    {
        parent::__construct($column, $label);

        $this->{$this->fieldName}();
    }
}
