<?php

namespace Poppy\MgrApp\Http\MgrApp;

use Illuminate\Support\Str;
use Poppy\MgrApp\Classes\Filter\FilterPlugin;
use Poppy\MgrApp\Classes\Filter\Query\Scope;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Grid\Tools\Actions;
use Poppy\MgrApp\Classes\Table\Render\ActionsRender;
use Poppy\MgrApp\Classes\Table\TablePlugin;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;
use Poppy\System\Models\PamRoleAccount;

class GridPamAccount extends GridBase
{

    public string $title = '账号管理';

    /**
     * @inheritDoc
     */
    public function table(TablePlugin $table)
    {
        $table->add('id', "ID")->sortable()->width(90, true)->align('center');
        $table->add('username', "用户名");
        $table->add('mobile', "手机号");
        $table->add('email', "邮箱");
        $table->add('login_times', "登录次数")->width(90, true)->align('center');
        $table->add('created_at', "操作时间")->width(170, true);
        $pam = $this->pam;
        $table->add('handle', '操作')->asAction(function (ActionsRender $actions) use ($pam) {
            $row = $actions->getRow();
            $actions->default(['plain', 'circle', 'only']);
            $actions->page('修改密码', route('py-mgr-app:api.pam.password', [data_get($row, 'id')]), 'form')->icon('Key');
            if ($pam->can('disable', $row)) {
                $actions->page('禁用', route_url('py-mgr-app:api.pam.disable', [data_get($row, 'id')]), 'form')->icon('MuteNotification')->danger();
            }
            if ($pam->can('enable', $row)) {
                $actions->page('启用', route_url('py-mgr-app:api.pam.enable', [data_get($row, 'id')]), 'form')->icon('Select')->success();
            }
            $actions->page("编辑", route_url('py-mgr-app:api.pam.establish', [data_get($row, 'id')]), 'form')->icon('Edit');
        })->width(150, true)->fixed();
    }

    /**
     * @inheritDoc
     */
    public function filter(FilterPlugin $filter)
    {
        $types = PamAccount::kvType();
        $type  = input(Scope::QUERY_NAME);
        foreach ($types as $t => $v) {
            if (!$type) {
                $type = $t;
            }
            $filter->scope($t, $v)->where('type', $t);
        }
        $roles = PamRole::getLinear($type);
        $filter->where(function ($query) {
            $passport = input('passport');
            $type     = PamAccount::passportType($passport);
            if ($type === PamAccount::REG_TYPE_MOBILE && !Str::contains($passport, '-')) {
                // 默认拼接国内手机号
                $passport = '86-' . $passport;
            }
            $query->where($type, $passport);
        }, '手机/用户名/邮箱', 'passport')->asText('手机/用户名/邮箱');
        $filter->where(function ($query) {
            $roleId      = data_get($this, 'input');
            $account_ids = PamRoleAccount::where('role_id', $roleId)->pluck('account_id');
            $query->whereIn('id', $account_ids);
        }, '用户角色', 'role_id')->asSelect($roles, '选择角色');
    }


    public function quick(Actions $actions)
    {
        $actions->page('新增账号', route_url('py-mgr-app:api.pam.establish'), 'form')->icon('CirclePlus');
    }
}
