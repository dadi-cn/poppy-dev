<?php


namespace Demo\App\Grid;

use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Grid\Tools\Interactions;
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
    public function batch(Interactions $actions)
    {
        $actions->request('批量删除', route('demo:api.grid.request', ['batch']))->confirm();
        $actions->page('批量调整', route('demo:api.form.auto', ['field-table']), 'form');
    }
}
