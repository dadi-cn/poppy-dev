<?php

namespace Poppy\MgrApp\Classes\Grid\Filter\Query;

class NotIn extends In
{
    /**
     * @inheritDoc
     */
    protected string $query = 'whereNotIn';
}
