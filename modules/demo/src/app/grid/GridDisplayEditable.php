<?php


namespace Demo\App\Grid;

use Demo\Models\DemoWebapp;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Widgets\TableWidget;

/**
 * 快捷编辑
 * @mixin DemoWebapp
 */
class GridDisplayEditable extends GridBase
{
    public string $title = '快捷编辑';

    /**
     * @inheritDoc
     */
    public function table(TableWidget $table)
    {
        $table->add('id', 'ID')->quickId();
        $table->add('sort', '行内编辑')->editable();
        $table->add('sort-custom', '行内编辑(自定义地址)')->editable(function () {
            return $this->sort;
        }, route('demo:api.mgr_app.grid_request', ['error']), 'custom-sort-field');
        $table->add('is_open', 'SwitchEdit')->switchable();
    }
}
