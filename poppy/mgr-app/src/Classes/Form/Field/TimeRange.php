<?php

namespace Poppy\MgrApp\Classes\Form\Field;

use Poppy\MgrApp\Classes\Form\FormItem;

class TimeRange extends FormItem
{

    protected string $format = 'HH:mm:ss';

    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->setAttribute('format', $this->format);
    }

    public function placeholders($start, $end): self
    {
        $this->setAttribute('start-placeholder', $start);
        $this->setAttribute('end-placeholder', $end);
        return $this;
    }
}
