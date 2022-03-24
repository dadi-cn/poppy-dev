<?php

namespace Poppy\MgrApp\Classes\Form\Field;

class MonthRange extends DateRange
{
    protected string $type = 'monthrange';

    protected string $format = 'YYYY-MM';
}
