<?php

namespace Poppy\MgrPage\Classes\Form\Field;

use Poppy\MgrPage\Classes\Form\Field;
use Poppy\MgrPage\Classes\Form\Traits\PlainInput;

class Date extends Field
{
    use PlainInput;

    protected $options = [
        'type' => 'date',
    ];

    protected $attributes = [
        'style' => 'width: 110px',
    ];

    protected $view = 'py-mgr-page::tpl.form.date';

    public function render()
    {

        $this->prepend('<i class="fa fa-calendar fa-fw"></i>');
        $this->addVariables([
            'prepend' => $this->prepend,
            'options' => $this->options,
        ]);
        return parent::render();
    }
}
