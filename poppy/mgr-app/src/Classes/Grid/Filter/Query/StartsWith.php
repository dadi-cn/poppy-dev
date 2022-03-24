<?php

namespace Poppy\MgrApp\Classes\Grid\Filter\Query;


class StartsWith extends Like
{
    protected string $exprFormat = '{value}%';
}
