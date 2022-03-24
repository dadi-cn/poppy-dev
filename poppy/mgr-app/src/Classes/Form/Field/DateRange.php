<?php

namespace Poppy\MgrApp\Classes\Form\Field;

use Poppy\MgrApp\Classes\Form\FormItem;

class DateRange extends FormItem
{

    protected string $type = 'daterange';

    protected string $format = 'YYYY-MM-DD';

    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->setAttribute('type', $this->type);
        $this->setAttribute('format', $this->format);
    }


    public function placeholders($start, $end): self
    {
        $this->setAttribute('start-placeholder', $start);
        $this->setAttribute('end-placeholder', $end);
        return $this;
    }
}
