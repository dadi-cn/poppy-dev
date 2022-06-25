<?php

namespace Poppy\MgrApp\Classes\Table\Show;

use Closure;

trait WithDefault
{

    protected function withInlineSaveDisabled($cb = null, $disableCb = null): self
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

    /**
     * 是否禁用当前数据
     * @param $disableCb
     * @return WithDefault
     */
    protected function withModifyDisabled($disableCb = null): self
    {
        return $this->display(function ($value) use ($disableCb) {
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
