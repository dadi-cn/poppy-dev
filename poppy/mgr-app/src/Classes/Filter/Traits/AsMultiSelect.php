<?php

namespace Poppy\MgrApp\Classes\Filter\Traits;

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