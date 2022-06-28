<?php


namespace Demo\App\GridNpk;

use Demo\Models\DemoWebappNpk;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Table\TablePlugin;

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
    public function table(TablePlugin $table)
    {
        $table->add('sort', '行内编辑')->asInlineSaveText();
        $table->add('is_open', 'Switch')->asInlineSaveOnOff();
    }
}
