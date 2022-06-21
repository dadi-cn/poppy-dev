<?php

namespace Poppy\MgrApp\Classes\Grid\Column\Show;

use Closure;

trait AsDefault
{

    protected function asDefaultDisabled($cb = null, $disableCb = null): self
    {
        return $this->display(function ($value) use ($cb, $disableCb) {
            if ($cb instanceof Closure) {
                $cb    = $cb->bindTo($this);
                $value = call_user_func_array($cb, [$this]);
            }
            $disable = false;
            if ($disableCb instanceof Closure) {
                $disableCb = $disableCb->bindTo($this);
                $disable   = call_user_func_array($disableCb, [$this]);
            }
            return [
                'value'    => $value,
                'disabled' => $disable,
            ];
        });
    }
}
