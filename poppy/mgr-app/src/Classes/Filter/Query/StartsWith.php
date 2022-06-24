<?php

namespace Poppy\MgrApp\Classes\Filter\Query;


class StartsWith extends Like
{
    protected string $exprFormat = '{value}%';
}
