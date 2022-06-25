<?php

namespace Poppy\MgrApp\Classes\Table\Show;

use Closure;
use Poppy\MgrApp\Classes\Table\Option\SelectOption;
use Poppy\MgrApp\Classes\Table\Option\SelectQueryOption;

trait AsSelect
{
    /**
     * 追加编辑模式
     */
    public function asInlineSaveSelect(Closure $cb = null, Closure $disableCb = null): SelectQueryOption
    {
        $this->type     = 'select';
        $this->editable = 'inline-save';
        return tap(new SelectQueryOption(), function ($option) use ($cb, $disableCb) {
            $this->setEditAttr($option);
            $this->withInlineSaveDisabled($cb, $disableCb);
        });
    }

    /**
     * 追加编辑模式
     */
    public function asModifySelect(Closure $disableCb = null): SelectOption
    {
        $this->type     = 'select';
        $this->editable = 'modify';
        return tap(new SelectOption(), function ($option) use ($disableCb) {
            $this->setEditAttr($option);
            $this->withModifyDisabled($disableCb);
        });
    }


    /**
     * @param Closure|null $cb
     * @param Closure|null $disableCb
     * @return SelectQueryOption
     * @deprecated 4.0-dev
     * @see        asInlineSaveSelect
     */
    public function editAsSelect(Closure $cb = null, Closure $disableCb = null): SelectQueryOption
    {
        return $this->asInlineSaveSelect($cb, $disableCb);
    }
}
