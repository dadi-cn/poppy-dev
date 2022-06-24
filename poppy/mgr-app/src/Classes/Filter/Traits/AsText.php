<?php

namespace Poppy\MgrApp\Classes\Filter\Traits;

trait AsText
{

    public function asText($placeholder = ''): self
    {
        $this->type = 'text';
        $this->placeholder($placeholder);
        return $this;
    }
}
