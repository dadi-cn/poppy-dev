<?php

namespace Poppy\MgrPage\Classes\Form\Field;

class Decimal extends Text
{

    public function render()
    {
        $this->prepend('<i class="fa fa-terminal fa-fw"></i>')
            ->defaultAttribute('style', 'width: 130px');

        $this->addVariables([
            'type' => 'number',
        ]);

        return parent::render();
    }
}
