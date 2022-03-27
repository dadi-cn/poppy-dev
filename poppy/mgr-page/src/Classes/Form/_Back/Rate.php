<?php

namespace Poppy\MgrPage\Classes\Form\_Back;

use Poppy\MgrPage\Classes\Form\Field\Text;

class Rate extends Text
{
    public function render()
    {
        $this->prepend('')
            ->append('%')
            ->defaultAttribute('style', 'text-align:right;')
            ->defaultAttribute('placeholder', 0);

        return parent::render();
    }
}
