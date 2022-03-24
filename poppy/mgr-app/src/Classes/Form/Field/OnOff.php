<?php

namespace Poppy\MgrApp\Classes\Form\Field;

use Poppy\MgrApp\Classes\Form\FormItem;

class OnOff extends FormItem
{
    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->setAttribute('active-value', '1');
        $this->setAttribute('inactive-value', '0');
    }

    /**
     * 设置开关的展示文字[开/关]
     * @return $this
     */
    public function text($on, $off = ''): self
    {
        $this->setAttribute('active-text', $on);
        $this->setAttribute('inactive-text', $off);
        return $this;
    }


    public function yn($on = 'Y', $off = 'N'): self
    {
        $this->setAttribute('active-value', $on);
        $this->setAttribute('inactive-value', $off);
        return $this;
    }
}
