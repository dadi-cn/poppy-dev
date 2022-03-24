<?php

namespace Poppy\MgrApp\Classes\Form\Field;

use Poppy\Framework\Validation\Rule;

class Mobile extends Text
{

    public function __construct($name = '', $label = '')
    {
        parent::__construct($name, $label);
        $this->rules([
            Rule::mobile(),
        ]);
        $this->prefixIcon('Cellphone');
    }
}
