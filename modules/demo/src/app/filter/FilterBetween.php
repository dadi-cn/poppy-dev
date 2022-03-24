<?php


namespace Demo\App\Filter;

use Poppy\MgrApp\Classes\Widgets\FilterWidget;
use Poppy\MgrApp\Classes\Widgets\TableWidget;
use Poppy\MgrApp\Http\Grid\GridBase;

/**
 * 按钮
 */
class FilterBetween extends GridBase
{
    public string $title = 'Between';

    public string $description = '描述';

    /**
     * @inheritDoc
     */
    public function table(TableWidget $table)
    {
        $table->add('id', 'ID')->quickId()->sortable();
        $table->add('title', '状态[1-5]')->quickTitle();
        $table->add('status', '状态[1-5]')->quickId();
        $table->add('account_id', 'UID[1-50]')->quickId(true);
        $table->add('post_at', '发布时间')->quickDatetime();
        $table->add('delete_at', '删除时间')->quickDatetime();
        $table->add('modify_at', '修改时间');
        $table->add('rename_at', '命名时间');
    }

    /**
     * @inheritDoc
     */
    public function filter(FilterWidget $filter)
    {
        $filter->between('status', '状态(Text)')->placeholder(['开始', '结束']);
        $filter->between('account_id', 'UID(Select)')->asSelect(range(1, 50), ['Start', 'End']);
        $filter->between('post_at', '发布时间(Datetime)')->asDatetime(['Start', 'End']);
        $filter->between('delete_at', '删除时间(Date)')->asDate(['Start', 'End']);
        $filter->between('modify_at', '修改时间(Month)')->asMonth(['Start 月份', 'End 月份']);
        $filter->between('rename_at', '重命名时间(Year)')->asYear(['Start 年份', 'End 年份']);
        $filter->action();
    }
}
