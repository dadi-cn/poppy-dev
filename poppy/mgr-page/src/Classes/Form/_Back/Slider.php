<?php

namespace Poppy\MgrPage\Classes\Form\_Back;

use Poppy\MgrPage\Classes\Form\Field;

class Slider extends Field
{


    protected $options = [
        'type'     => 'single',
        'prettify' => false,
        'hasGrid'  => true,
    ];

    public function render()
    {
        return parent::render();
    }
}
