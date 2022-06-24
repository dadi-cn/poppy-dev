<?php


namespace Demo\App\Grid;

use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Grid\Tools\Actions;
use Poppy\MgrApp\Classes\Table\TablePlugin;

/**
 * 按钮
 */
class GridBatchActions extends GridBase
{
    public string $title = '批量操作';

    public string $description = '描述';

    /**
     * @inheritDoc
     */
    public function table(TablePlugin $table)
    {
        $table->add('id', 'ID')->quickId()->sortable();
        $table->add('title', '标题')->width(200)->ellipsis();
        $table->add('status', '状态')->display(function () {
            $defs = [
                1 => '未发布',
                2 => '草稿',
                5 => '待审核',
                3 => '已发布',
                4 => '已删除',
            ];
            return $defs[data_get($this, 'status')] ?? '-';
        });
        $table->add('birth_at', '发布时间')->quickDatetime();
    }

    /**
     * @inheritDoc
     */
    public function batch(Actions $actions)
    {
        $actions->request('批量删除', 'api/demo/grid_request/success')->confirm();
    }
}
