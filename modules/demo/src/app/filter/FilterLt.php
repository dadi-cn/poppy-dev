<?php

namespace Demo\App\Filter;

use Demo\Models\DemoUser;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrApp\Classes\Widgets\FilterWidget;
use Poppy\MgrApp\Classes\Widgets\TableWidget;
use Poppy\MgrApp\Http\Grid\GridBase;

class FilterLt extends GridBase
{
    public string $title = 'Lt 小于';

    /**
     * @inheritDoc
     */
    public function table(TableWidget $table)
    {
        $table->add('id', 'ID')->quickId();
        $table->add('user.id', 'UID')->quickId(true)->align('center');
        $table->add('title', '标题')->quickTitle();
        $table->add('birth_at', '出生时间')->quickDatetime()->sortable();
        $table->add('post_at', '发布时间')->quickDatetime()->sortable();
        $table->add('created_at', '创建时间')->quickDatetime()->sortable();
        $table->add('modify_at', '修改时间')->quickDatetime()->sortable();
    }


    /**
     * @inheritDoc
     */
    public function filter(FilterWidget $filter)
    {
        $filter->action();
        $filter->lt('id', 'ID')->asText();
        $filter->lt('account_id', 'Uid小于(Select)')->asSelect(DemoUser::where('id', '>', 35)->pluck('id', 'id')->toArray(), '选择用户');
        $filter->lt('birth_at', '出生年月<(年)')->asYear('按年查询');
        $filter->lt('post_at', '发布时间<(天)')->asDate('按天查询');
        $filter->lt('created_at', '创建时间<(月份)')->asMonth('按月查询');
        $filter->lt('modify_at', '修改时间<(日期/时间)')->asDatetime('按日期时间查询');
    }
}
