<?php


namespace Demo\App\Grid;

use Demo\Models\DemoWebapp;
use Poppy\Framework\Helper\StrHelper;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Table\TablePlugin;

/**
 * 快捷列表
 * @mixin DemoWebapp
 */
class GridDisplayHidden extends GridBase
{
    public string $title = '隐藏数据';

    /**
     * @inheritDoc
     */
    public function table(TablePlugin $table)
    {
        $table->add('id', 'QuickId')->quickId();
        $table->add('title', 'Hide')->asHidden();
        $table->add('title-alt', 'Hide(自定义显示)')->asHidden(function () {
            /** @var $this DemoWebapp */
            return StrHelper::hideContact($this->title);
        })->query('title', route('demo:api.mgr_app.grid_view'));
        $table->add('title-error', 'Hide(查找错误)')->asHidden(function () {
            /** @var $this DemoWebapp */
            return StrHelper::hideContact($this->title);
        })->query('title', route('demo:api.grid.request', ['error']));
    }
}
