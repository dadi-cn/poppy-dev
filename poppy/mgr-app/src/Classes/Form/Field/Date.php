<?php

namespace Poppy\MgrApp\Classes\Form\Field;

use Poppy\MgrApp\Classes\Form\FormItem;
use Poppy\MgrApp\Classes\Form\Traits\UsePlaceholder;

class Date extends FormItem
{

    use UsePlaceholder;

    protected string $type = 'date';

    protected string $format = 'YYYY-MM-DD';

    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->setAttribute('type', $this->type);
        $this->setAttribute('format', $this->format);
    }
}
