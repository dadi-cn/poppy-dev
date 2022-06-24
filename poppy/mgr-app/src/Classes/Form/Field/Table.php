<?php

namespace Poppy\MgrApp\Classes\Form\Field;

use Closure;
use Poppy\MgrApp\Classes\Form\FormItem;
use Poppy\MgrApp\Classes\Table\TablePlugin;

class Table extends FormItem
{
    protected TablePlugin $table;

    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->table = new TablePlugin();
    }

    public function cols(Closure $closure)
    {
        $closure->call($this, $this->table);
        $this->setAttribute('cols', $this->table->struct());
    }
}
