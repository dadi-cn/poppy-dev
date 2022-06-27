<?php


namespace Demo\App\Grid;

use Demo\Models\DemoWebapp;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Table\TablePlugin;

/**
 * @mixin DemoWebapp
 */
class GridEditAsOnOff extends GridBase
{
    public string $title = '快捷编辑(OnOff)';

    /**
     * @inheritDoc
     */
    public function table(TablePlugin $table)
    {
        $table->add('id', 'ID')->quickId();
        $table->add('loading', '行内编辑')->asOnOff();
        $table->add('loading-alt', '行内编辑(字段更名)')->asInlineSaveOnOff(function () {
            return $this->loading;
        })->query('loading');
        $table->add('loading-disable', '行内编辑(禁用部分)')->asInlineSaveOnOff(function () {
            return $this->loading;
        }, function () {
            return $this->id % 3 === 0;
        })->query('loading');
        $table->add('loading-error', '行内编辑(请求错误)')->asInlineSaveOnOff(function () {
            return $this->loading;
        })->query('loading', route('demo:api.grid.request', ['error']));
    }
}
