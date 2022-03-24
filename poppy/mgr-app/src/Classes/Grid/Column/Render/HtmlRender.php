<?php

namespace Poppy\MgrApp\Classes\Grid\Column\Render;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Fluent;

class HtmlRender extends Render
{

    protected string $type = 'html';

    public function render(): Jsonable
    {
        return new Fluent([
            'value' => $this->value
        ]);
    }
}
