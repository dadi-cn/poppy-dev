<?php

namespace Poppy\MgrApp\Classes\Form\Field;

class Year extends Date
{
    protected string $type = 'year';

    protected string $format = 'YYYY';
}
