<?php

namespace Poppy\MgrApp\Http\Grid;

use Poppy\MgrApp\Classes\Widgets\FilterWidget;
use Poppy\MgrApp\Classes\Widgets\TableWidget;

/**
 * 列表 PamLog
 */
class GridPamLog extends GridBase
{

    public string $title = '登录日志';

    /**
     */
    public function table(TableWidget $table)
    {
        $table->add('id', "ID")->sortable()->width(80);
        $table->add('pam.username', "用户名");
        $table->add('created_at', "操作时间");
        $table->add('ip', "IP地址");
        $table->add('type', "状态");
        $table->add('area_text', "说明");
    }


    public function filter(FilterWidget $filter)
    {
        $filter->equal('account_id', '用户ID')->asText('用户ID');
        $filter->equal('ip', 'IP地址')->asText('用户IP');
        $filter->like('area_text', '登录地区');
    }
}
