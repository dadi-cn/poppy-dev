<?php

namespace Demo\App\Filter;

use Demo\Models\DemoUser;
use Poppy\MgrApp\Classes\Filter\FilterPlugin;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Table\TablePlugin;

class FilterLte extends GridBase
{
    public string $title = 'Lt 小于等于';

    /**
     * @inheritDoc
     */
    public function table(TablePlugin $table)
    {
        $table->add('id', 'ID')->quickId()->sortable();
        $table->add('user.id', 'UID')->quickId()->align('center');
        $table->add('title', '标题')->quickTitle();
        $table->add('birth_at', '出生时间')->quickDatetime()->sortable();
        $table->add('post_at', '发布时间')->quickDatetime()->sortable();
        $table->add('created_at', '创建时间')->quickDatetime()->sortable();
        $table->add('modify_at', '修改时间')->quickDatetime()->sortable();
    }


    /**
     * @inheritDoc
     */
    public function filter(FilterPlugin $filter)
    {
        $filter->action();
        $filter->lte('id', 'ID')->asText();
        $filter->lte('account_id', 'Uid<=(Select)')->asSelect(DemoUser::where('id', '<', 10)->pluck('id', 'id')->toArray(), '选择用户');
        $filter->lte('birth_at', '出生年月<=(年)')->asYear('按年查询');
        $filter->lte('post_at', '发布时间<=(天)')->asDate('按天查询');
        $filter->lte('created_at', '创建时间<=(月份)')->asMonth('按月查询');
        $filter->lte('modify_at', '修改时间<=(日期/时间)')->asDatetime('按日期时间查询');
    }
}
