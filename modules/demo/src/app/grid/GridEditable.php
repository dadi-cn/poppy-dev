<?php


namespace Demo\App\Grid;

use Demo\Models\DemoWebapp;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Widgets\TableWidget;

/**
 * 快捷列表
 * @mixin DemoWebapp
 */
class GridEditable extends GridBase
{
    public string $title = '快捷搜索';

    /**
     * @inheritDoc
     */
    public function table(TableWidget $table)
    {
        $table->add('id', 'QuickId')->quickId();
        $table->add('sort', 'QuickSort')->editable();
    }
}
