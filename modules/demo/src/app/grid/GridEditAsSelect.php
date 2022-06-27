<?php


namespace Demo\App\Grid;

use Demo\Models\DemoWebapp;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Table\TablePlugin;

/**
 * @mixin DemoWebapp
 */
class GridEditAsSelect extends GridBase
{
    public string $title = '快捷编辑(选择器)';

    /**
     * @inheritDoc
     */
    public function table(TablePlugin $table)
    {
        $table->add('id', 'ID')->quickId();
        $table->add('status', '行内编辑')
            ->asInlineSaveSelect()->options(DemoWebapp::kvStatus());
        $table->add('status-alt', '行内编辑(字段更名)')->asInlineSaveSelect(function () {
            return $this->status;
        })->query('status')->options(DemoWebapp::kvStatus());
        $table->add('status-disable', '行内编辑(ID%3==0)')->asInlineSaveSelect(function () {
            return $this->status;
        }, function () {
            return $this->id % 3 === 0;
        })->query('status')->options(DemoWebapp::kvStatus());
        $table->add('status-error', '行内编辑(请求错误)')->asInlineSaveSelect(function () {
            return $this->status;
        })->query('status', route('demo:api.grid.request', ['error']))->options(DemoWebapp::kvStatus());
    }
}
