<?php

namespace Poppy\MgrApp\Classes\Form\Field;

class Tags extends MultiSelect
{
    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->setAttribute('allow-create', true);
        $this->setAttribute('filterable', true);
    }
}
