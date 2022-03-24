<?php

namespace Poppy\MgrApp\Classes\Form\Field;

class Currency extends Decimal
{
    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->digits(2);
        $this->prefixIcon('Money');
    }
}
