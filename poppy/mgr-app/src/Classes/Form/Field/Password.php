<?php

namespace Poppy\MgrApp\Classes\Form\Field;

class Password extends Text
{
    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->setAttribute('show-password', true);
    }
}
