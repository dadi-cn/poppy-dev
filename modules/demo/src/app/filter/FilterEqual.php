<?php

namespace Demo\App\Filter;

use Demo\Models\DemoUser;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrApp\Classes\Widgets\FilterWidget;
use Poppy\MgrApp\Classes\Widgets\TableWidget;
use Poppy\MgrApp\Http\Grid\GridBase;

class FilterEqual extends GridBase
{
    public string $title = 'Equal 相等';

    /**
     * @inheritDoc
     */
    public function table(TableWidget $table)
    {
        $table->add('id', 'ID')->quickId();
        $table->add('user.nickname', '昵称')->quickTitle()->align('center');
        $table->add('title', '标题')->quickTitle();
        $table->add('birth_at', '出生时间')->quickDatetime();
        $table->add('post_at', '发布时间')->quickDatetime();
        $table->add('created_at', '创建时间')->quickDatetime();
        $table->add('modify_at', '修改时间')->quickDatetime();
        $table->add('is_open', '启用')->display(function () {
            return data_get($this, 'is_open') ? '启用' : '禁用';
        });
    }


    /**
     * @inheritDoc
     */
    public function filter(FilterWidget $filter)
    {
        $filter->action();
        $filter->equal('id', 'ID')->asText();
        $filter->equal('account_id', 'Uid(Select)')->asSelect(DemoUser::where('id', '<', 20)->pluck('nickname', 'id')->toArray(), '选择用户');
        $filter->equal('birth_at', '出生年月(年)')->asYear('按年查询');
        $filter->equal('post_at', '发布时间(天)')->asDate('按天查询');
        $filter->equal('created_at', '创建时间(月份)')->asMonth('按月查询');
        $filter->equal('is_open', '是否启用')->width(2)->asSelect([
            0 => '未启用',
            1 => '启用'
        ], '启用禁用');
        $filter->equal('modify_at', '修改时间(日期/时间)')->asDatetime('按日期时间查询');
        $filter->equal('modify_at', '修改时间(日期/时间)')->asDatetime('按日期时间查询');
    }
}
