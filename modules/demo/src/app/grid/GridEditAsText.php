<?php


namespace Demo\App\Grid;

use Demo\Models\DemoWebapp;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Widgets\TableWidget;

/**
 * @mixin DemoWebapp
 */
class GridEditAsText extends GridBase
{
    public string $title = '快捷编辑';

    /**
     * @inheritDoc
     */
    public function table(TableWidget $table)
    {
        $table->add('id', 'ID')->quickId();
        $table->add('sort', '行内编辑')->copyable()->editAsText();
        $table->add('sort-alt', '行内编辑(字段更名)')->copyable()->editAsText(function () {
            return $this->sort;
        })->query('sort');
        $table->add('sort-disable', '行内编辑(禁用部分)')->copyable()->editAsText(function () {
            return $this->sort;
        }, function () {
            return $this->id % 3 === 0;
        })->query('sort');
        $table->add('sort-error', '行内编辑(请求错误)')->copyable()->editAsText(function () {
            return $this->sort;
        })->query('sort', route('demo:api.mgr_app.grid_request', ['error']));

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
