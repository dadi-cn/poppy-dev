<?php

namespace Poppy\MgrApp\Classes\Form\Field;

final class MultiImage extends MultiFile
{
    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->image();
    }
}
