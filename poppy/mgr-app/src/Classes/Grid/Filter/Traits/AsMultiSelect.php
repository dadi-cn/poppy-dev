<?php

namespace Poppy\MgrApp\Classes\Grid\Filter\Traits;

trait AsMultiSelect
{

    public function asMultiSelect($options, $placeholder): self
    {
        $this->type = 'multi-select';
        $this->options($options);
        $this->placeholder($placeholder);
        return $this;
    }
}