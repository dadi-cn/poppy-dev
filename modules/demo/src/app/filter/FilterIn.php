<?php

namespace Demo\App\Filter;

use Demo\Models\DemoUser;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrApp\Classes\Widgets\FilterWidget;
use Poppy\MgrApp\Http\Grid\GridBase;

class FilterIn extends GridBase
{
    public string $title = 'In';

    /**
     * @inheritDoc
     */
    public function columns()
    {
        $this->column('id', 'ID')->quickId()->sortable();
        $this->column('user.id', 'UID')->quickId()->align('center');
        $this->column('title', '标题')->quickTitle();
        $this->column('status', '状态')->display(function () {
            $defs = [
                1 => '未发布',
                2 => '草稿',
                5 => '待审核',
                3 => '已发布',
                4 => '已删除',
            ];
            return $defs[data_get($this, 'status')] ?? '-';
        });
    }


    /**
     * @inheritDoc
     */
    public function filter(FilterWidget $filter)
    {
        $filter->action();
        $filter->in('id', 'ID')->asMultiSelect(array_combine(range(1, 20), range(1, 20)), 'ID');
        $filter->in('account_id', 'Uid In(Select)')->asMultiSelect(DemoUser::where('id', '<', 10)->pluck('id', 'id')->toArray(), '选择用户');
    }
}
