<?php

namespace Poppy\MgrApp\Classes\Grid\Filter\Query;


class EndsWith extends Like
{
    protected string $exprFormat = '%{value}';
}
