<?php


namespace Demo\App\Grid;

use Demo\Models\DemoWebapp;
use Poppy\Framework\Helper\StrHelper;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Widgets\TableWidget;

/**
 * 快捷列表
 * @mixin DemoWebapp
 */
class GridHidden extends GridBase
{
    public string $title = '隐藏数据';

    /**
     * @inheritDoc
     */
    public function table(TableWidget $table)
    {
        $table->add('id', 'QuickId')->quickId();
        $table->add('title', 'Hide')->hidden(null, route('demo:api.mgr_app.grid_view'));
        $table->add('title-custom', 'Hide')->hidden(function () {
            /** @var $this DemoWebapp */
            return StrHelper::hideContact($this->title);
        }, route('demo:api.mgr_app.grid_view'), 'title');
    }
}
