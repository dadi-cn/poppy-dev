<?php

namespace Poppy\MgrPage\Classes\Form\_Back;

use Poppy\MgrPage\Classes\Form\Field\Text;

class Icon extends Text
{
    protected $default = 'fa-pencil';

    public function render()
    {

        $this->prepend('<i class="fa fa-pencil fa-fw"></i>')
            ->defaultAttribute('style', 'width: 140px');

        return parent::render();
    }
}
