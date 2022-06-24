<?php

namespace Poppy\MgrApp\Http\MgrApp;

use Poppy\MgrApp\Classes\Filter\FilterPlugin;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Table\TablePlugin;

/**
 * 列表 PamLog
 */
class GridPamLog extends GridBase
{

    public string $title = '登录日志';

    /**
     */
    public function table(TablePlugin $table)
    {
        $table->add('id', "ID")->sortable()->width(80);
        $table->add('pam.username', "用户名");
        $table->add('created_at', "操作时间");
        $table->add('ip', "IP地址");
        $table->add('type', "状态");
        $table->add('area_text', "说明");
    }


    public function filter(FilterPlugin $filter)
    {
        $filter->equal('account_id', '用户ID')->asText('用户ID');
        $filter->equal('ip', 'IP地址')->asText('用户IP');
        $filter->like('area_text', '登录地区');
    }
}
