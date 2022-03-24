<?php

namespace Poppy\MgrApp\Classes\Grid\Column\Render;

use Closure;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Fluent;

/**
 * 将字段渲染为链接
 */
class LinkRender extends Render
{

    protected string $type = 'link';

    public function render($callback = '', $target = '_blank'): Jsonable
    {
        if ($callback instanceof Closure) {
            $callback = $callback->bindTo($this->row);
            $href     = call_user_func_array($callback, [$this->row]);
        } else {
            $href = $callback ?: $this->value;
        }

        return new Fluent([
            'url'    => $href,
            'target' => $target,
            'text'   => $this->value,
        ]);
    }
}
