<?php


namespace Demo\App\GridNpk;

use Demo\Models\DemoWebappNpk;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Widgets\TableWidget;

/**
 * 快捷编辑
 * @mixin DemoWebappNpk
 */
class GridDisplayEditable extends GridBase
{
    public string $title = '快捷编辑(无主键禁用状态)';

    /**
     * @inheritDoc
     */
    public function table(TableWidget $table)
    {
        $table->add('sort', '行内编辑')->editable();
        $table->add('is_open', 'Switch')->switchable();
    }
}
