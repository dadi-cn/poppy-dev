<?php

namespace Poppy\MgrPage\Classes\Grid\Displayer;

use Closure;

class Link extends AbstractDisplayer
{
    public function display($callback = '', $target = '_blank')
    {
        if ($callback instanceof Closure) {
            $callback = $callback->bindTo($this->row);
            $href     = call_user_func_array($callback, [$this->row]);
        }
        else {
            $href = $callback ?: $this->value;
        }

        return "<a href='$href' target='$target'>$this->value</a>";
    }
}
