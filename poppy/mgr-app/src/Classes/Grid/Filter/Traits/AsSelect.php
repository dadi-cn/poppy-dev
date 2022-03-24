<?php

namespace Poppy\MgrApp\Classes\Grid\Filter\Traits;

trait AsSelect
{

    public function asSelect($options, $placeholder, $filterable = false): self
    {
        $this->type = 'select';
        $this->options($options);
        $this->placeholder($placeholder);
        if ($filterable) {
            $this->filterable();
        }
        return $this;
    }
}