<?php

namespace Poppy\MgrPage\Classes\Form\Field;

use Poppy\Framework\Validation\Rule;

class Number extends Text
{

    protected $type = 'number';


    public function __construct($column = '', $arguments = [])
    {
        parent::__construct($column, $arguments);
        $this->rules[] = Rule::numeric();
    }


    public function render()
    {
        $this->default($this->default);

        $this->prepend('')->defaultAttribute('style', 'width: 100px');

        return parent::render();
    }
}
