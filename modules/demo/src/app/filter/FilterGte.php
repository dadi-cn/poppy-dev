<?php

namespace Demo\App\Filter;

use Demo\Models\DemoUser;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrApp\Classes\Widgets\FilterWidget;
use Poppy\MgrApp\Classes\Widgets\TableWidget;
use Poppy\MgrApp\Http\Grid\GridBase;

class FilterGte extends GridBase
{
    public string $title = 'Gte 大于等于';

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
        $table->add('created_at', '创建时间')->quickDatetime();
        $table->add('modify_at', '修改时间')->quickDatetime();
    }


    /**
     * @inheritDoc
     */
    public function filter(FilterWidget $filter)
    {
        $filter->action();
        $filter->gte('id', 'ID')->asText();
        $filter->gte('account_id', 'Uid >=(Select)')->asSelect(DemoUser::where('id', '>', 35)->pluck('id', 'id')->toArray(), '选择用户');
        $filter->gte('birth_at', '出生年月>=(年)')->asYear('按年查询');
        $filter->gte('post_at', '发布时间>=(天)')->asDate('按天查询');
        $filter->gte('created_at', '创建时间>=(月份)')->asMonth('按月查询');
        $filter->gte('modify_at', '修改时间>=(日期/时间)')->asDatetime('按日期时间查询');
    }
}
