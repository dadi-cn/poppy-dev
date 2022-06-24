<?php

namespace Poppy\MgrApp\Http\MgrApp;

use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Table\Render\ActionsRender;
use Poppy\MgrApp\Classes\Table\TablePlugin;

class GridPamToken extends GridBase
{

    public string $title = '登录用户管理';

    /**
     * @inheritDoc
     */
    public function table(TablePlugin $table)
    {
        $table->add('id', "ID")->sortable()->width(90, true)->align('center');
        $table->add('account_id', "用户ID")->width(90, true)->align('center');
        $table->add('device_type', "设备类型");
        $table->add('device_id', "设备ID")->width(320, false);
        $table->add('login_ip', "登录IP");
        $table->add('expired_at', "过期时间")->width(170, true);
        $table->action(function (ActionsRender $actions) {
            $row = $actions->getRow();
            $actions->default(['plain', 'circle', 'only']);
            $actions->request('禁用IP', route('py-mgr-app:api.pam.ban', [data_get($row, 'id'), 'ip']))->icon('MapLocation');
            $actions->request('禁用设备', route('py-mgr-app:api.pam.ban', [data_get($row, 'id'), 'device']))->icon('Cellphone');
            $actions->request("删除", route_url('py-mgr-app:api.pam.delete_token', [data_get($row, 'id')]))->icon('Close')->danger();
        })->width(150, true)->fixed();
    }
}
