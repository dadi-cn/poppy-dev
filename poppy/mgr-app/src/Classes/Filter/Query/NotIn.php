<?php

namespace Poppy\MgrApp\Classes\Filter\Query;

class NotIn extends In
{
    /**
     * @inheritDoc
     */
    protected string $query = 'whereNotIn';
}
