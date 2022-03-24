<?php

namespace Poppy\MgrApp\Classes\Form\Field;

class Month extends Date
{
    protected string $type = 'month';

    protected string $format = 'YYYY-MM';
}
