<?php

namespace Poppy\MgrApp\Classes\Table\Show;

use Closure;
use Poppy\MgrApp\Classes\Table\Option\OnOffOption;
use Poppy\MgrApp\Classes\Table\Option\OnOffQueryOption;

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
     * 编辑模式(非行内请求)
     */
    public function AsModifyOnOff(Closure $cb = null, Closure $disableCb = null): OnOffOption
    {
        $this->type     = 'on-off';
        $this->editable = 'modify';
        return tap(new OnOffOption(), function ($option) use ($cb, $disableCb) {
            $this->setEditAttr($option);
            $this->asDefaultDisabled($cb, $disableCb);
        });
    }

    /**
     * 追加编辑模式
     */
    public function asInlineSaveOnOff(Closure $cb = null, Closure $disableCb = null): OnOffQueryOption
    {
        $this->type     = 'on-off';
        $this->editable = 'inline-save';
        return tap(new OnOffQueryOption(), function ($option) use ($cb, $disableCb) {
            $this->setEditAttr($option);
            $this->asDefaultDisabled($cb, $disableCb);
        });
    }

    /**
     * 追加编辑模式
     * @deprecated 4.0-dev
     * @see        asInlineSaveOnOff
     */
    public function editAsOnOff(Closure $cb = null, Closure $disableCb = null): OnOffQueryOption
    {
        return $this->asInlineSaveOnOff($cb, $disableCb);
    }
}
