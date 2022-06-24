<?php

namespace Demo\App\Filter;

use Demo\Models\DemoUser;
use Poppy\MgrApp\Classes\Filter\FilterPlugin;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Table\TablePlugin;

class FilterNotIn extends GridBase
{
    public string $title = 'Not In';

    /**
     * @inheritDoc
     */
    public function table(TablePlugin $table)
    {
        $table->add('id', 'ID')->quickId()->sortable();
        $table->add('user.id', 'UID')->quickId()->align('center');
        $table->add('title', '标题')->quickTitle();
        $table->add('status', '状态')->display(function () {
            $defs = [
                1 => '未发布',
                2 => '草稿',
                5 => '待审核',
                3 => '已发布',
                4 => '已删除',
            ];
            return $defs[data_get($this, 'status')] ?? '-';
        })->sortable();
    }


    /**
     * @inheritDoc
     */
    public function filter(FilterPlugin $filter)
    {
        $filter->action();
        $filter->notIn('id', 'ID')->asMultiSelect(array_combine(range(1, 20), range(1, 20)), 'ID');
        $filter->notIn('account_id', 'Uid In(Select)')->asMultiSelect(DemoUser::where('id', '<', 10)->pluck('id', 'id')->toArray(), '选择用户');
    }
}
