<?php

namespace Poppy\MgrApp\Classes\Grid\Filter\Traits;

trait AsText
{

    public function asText($placeholder = ''): self
    {
        $this->type = 'text';
        $this->placeholder($placeholder);
        return $this;
    }
}
