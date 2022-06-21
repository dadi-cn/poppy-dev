<?php

namespace Poppy\MgrApp\Classes\Grid\Column\Show;

use Closure;
use Poppy\MgrApp\Classes\Grid\Column\Option\OnOffOption;

trait AsOnOff
{
    /**
     * 渲染为开关
     * @return self
     */
    public function asOnOff(): self
    {
        $this->type = 'on-off';
        return $this->display(function ($value) {
            if (in_array($value, [1, '1', 'true', true, 'Y', 'y', '是', 'yes', '是', 'on', 'ON'], true)) {
                return '<span class="material-symbols-outlined fs-3 text-success">toggle_on</span>';
            }
            else {
                return '<span class="material-symbols-outlined fs-3 text-danger">toggle_off</span>';
            }
        });
    }

    /**
     * 追加编辑模式
     */
    public function editAsOnOff(Closure $cb = null, Closure $disableCb = null): OnOffOption
    {
        $this->type     = 'on-off';
        $this->editable = 'on-off';
        return tap(new OnOffOption(), function ($option) use ($cb, $disableCb) {
            $this->setEditAttr($option);
            $this->asDefaultDisabled($cb, $disableCb);
        });
    }
}
