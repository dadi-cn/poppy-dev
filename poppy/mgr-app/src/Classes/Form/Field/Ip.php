<?php

namespace Poppy\MgrApp\Classes\Form\Field;

class Ip extends Text
{
    protected array $rules = [
        'ip',
    ];

    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->prefixIcon('Location');
    }
}
