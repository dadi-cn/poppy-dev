<?php


namespace Demo\App\Grid;

use Demo\Models\DemoWebapp;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Widgets\TableWidget;

/**
 * @mixin DemoWebapp
 */
class GridEditAsOnOff extends GridBase
{
    public string $title = '快捷编辑(OnOff)';

    /**
     * @inheritDoc
     */
    public function table(TableWidget $table)
    {
        $table->add('id', 'ID')->quickId();
        $table->add('loading', '行内编辑')->asOnOff();
        $table->add('loading-alt', '行内编辑(字段更名)')->editAsOnOff(function () {
            return $this->loading;
        })->query('loading');
        $table->add('loading-disable', '行内编辑(禁用部分)')->editAsOnOff(function () {
            return $this->loading;
        }, function () {
            return $this->id % 3 === 0;
        })->query('loading');
        $table->add('loading-error', '行内编辑(请求错误)')->editAsOnOff(function () {
            return $this->loading;
        })->query('loading', route('demo:api.mgr_app.grid_request', ['error']));

        //        $table->add('sort-custom', '行内编辑(自定义|错误)')->asEditable();


        //        $table->add('is_open', 'Switch')->switchable();
        //        $table->add('is_open-custom', 'Switch(自定义|错误)')->switchable(function () {
        //            return $this->is_open;
        //        }, route('demo:api.mgr_app.grid_request', ['error']), 'custom-is_open-field');
        //        $table->add('status', 'Select')->selectable([
        //            'options' => [
        //                1 => '启用',
        //                2 => '禁用',
        //            ],
        //        ]);
    }
}
