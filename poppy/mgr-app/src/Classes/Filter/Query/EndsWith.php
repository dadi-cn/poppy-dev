<?php

namespace Poppy\MgrApp\Classes\Filter\Query;


class EndsWith extends Like
{
    protected string $exprFormat = '%{value}';
}
