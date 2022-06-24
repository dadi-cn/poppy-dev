<?php


namespace Demo\App\Filter;

use Poppy\MgrApp\Classes\Filter\FilterPlugin;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Table\TablePlugin;

/**
 * 按钮
 */
class FilterScope extends GridBase
{
    public string $title = 'Scope';

    public string $description = '全局范围筛选';

    /**
     * @inheritDoc
     */
    public function table(TablePlugin $table)
    {
        $table->add('id', 'ID')->width(150)->sortable();
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
        $table->add('birth_at', '发布时间');
    }

    /**
     * @inheritDoc
     */
    public function filter(FilterPlugin $filter)
    {
        $filter->like('title', '标题');
        $filter->scope('none', '未发布')->where('status', 1);
        $filter->scope('wait', '待发布')->where('status', 2);
        $filter->action();
    }
}
