<?php

namespace Poppy\MgrPage\Classes\Grid\Filter;

class Day extends Date
{
    /**
     * @inheritDoc
     */
    protected $query = 'whereDay';

    /**
     * @var string
     */
    protected $fieldName = 'day';
}
