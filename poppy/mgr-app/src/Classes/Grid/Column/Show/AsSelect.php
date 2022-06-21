<?php

namespace Poppy\MgrApp\Classes\Grid\Column\Show;

use Closure;
use Poppy\MgrApp\Classes\Grid\Column\Option\SelectOption;

trait AsSelect
{
    /**
     * 追加编辑模式
     */
    public function editAsSelect(Closure $cb = null, Closure $disableCb = null): SelectOption
    {
        $this->type     = 'select';
        $this->editable = 'select';
        return tap(new SelectOption(), function ($option) use ($cb, $disableCb) {
            $this->setEditAttr($option);
            $this->asDefaultDisabled($cb, $disableCb);
        });
    }
}
