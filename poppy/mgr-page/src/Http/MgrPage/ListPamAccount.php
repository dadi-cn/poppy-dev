<?php

namespace Poppy\MgrPage\Http\MgrPage;

use Closure;
use Illuminate\Support\Str;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrPage\Classes\Grid\Column;
use Poppy\MgrPage\Classes\Grid\Displayer\Actions;
use Poppy\MgrPage\Classes\Grid\Filter;
use Poppy\MgrPage\Classes\Grid\ListBase;
use Poppy\MgrPage\Classes\Grid\Tools\BaseButton;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;
use Poppy\System\Models\PamRoleAccount;
use function data_get;
use function input;
use function route;
use function route_url;

class ListPamAccount extends ListBase
{

    public $title = '账号管理';

    /**
     * @inheritDoc
     * @throws ApplicationException
     */
    public function columns()
    {
        $this->column('id', "ID")->sortable()->width(80);
        $this->column('username', "用户名");
        $this->column('mobile', "手机号");
        $this->column('email', "邮箱");
        $this->column('login_times', "登录次数");
        $this->column('created_at', "操作时间");
        $this->column('type', "账号类型");
    }

    /**
     * @inheritDoc
     * @return Closure
     */
    public function filter(): Closure
    {
        return function (Filter $filter) {
            $type  = input(\Poppy\MgrPage\Classes\Grid\Filter\Scope::QUERY_NAME, PamAccount::TYPE_BACKEND);
            $roles = PamRole::getLinear($type);
            $filter->column(1 / 12, function (Filter $ft) {
                $ft->where(function ($query) {
                    $passport = input('passport');
                    $type     = PamAccount::passportType($passport);
                    if ($type === PamAccount::REG_TYPE_MOBILE && !Str::contains($passport, '-')) {
                        // 默认拼接国内手机号
                        $passport = '86-' . $passport;
                    }
                    $query->where($type, $passport);
                }, '手机/用户名/邮箱', 'passport');
            });
            $filter->column(1 / 12, function (Filter $column) use ($roles) {
                $column->where(function ($query) {
                    $roleId      = data_get($this, 'input');
                    $account_ids = PamRoleAccount::where('role_id', $roleId)->pluck('account_id');
                    $query->whereIn('id', $account_ids);
                }, '用户角色', 'role_id')->select($roles);
            });
            $types = PamAccount::kvType();
            foreach ($types as $t => $v) {
                $filter->scope($t, $v)->where('type', $t);
            }
        };
    }

    /**
     * @inheritDoc
     */
    public function actions()
    {
        $pam = $this->pam;
        $this->addColumn(Column::NAME_ACTION, '操作')
            ->displayUsing(Actions::class, [
                function (Actions $actions) use ($pam) {
                    /** @var PamAccount $item */
                    $item = $actions->row;
                    $actions->append([
                        new BaseButton('<i class="fa fa-key"></i>', route('py-mgr-page:backend.pam.password', [$item->id]), [
                            'title' => "修改密码",
                            'class' => 'J_iframe',
                        ]),
                        $pam->can('disable', $item) ?
                            new BaseButton('<i class="fa fa-unlock text-success"></i>', route_url('py-mgr-page:backend.pam.disable', [$item->id]), [
                                'title' => '当前启用, 点击禁用',
                                'class' => 'J_iframe',
                            ]) : null,
                        $pam->can('enable', $item) ?
                            new BaseButton('<i class="fa fa-lock"></i>', route_url('py-mgr-page:backend.pam.enable', [$item->id]), [
                                'title' => '当前禁用, 点击启用',
                                'class' => 'J_iframe',
                            ]) : null,
                        new BaseButton('<i class="fa fa-edit"></i>', route('py-mgr-page:backend.pam.establish', [$item->id]), [
                            'title' => "编辑[{$item->username}]",
                            'class' => 'J_iframe',
                        ]),
                    ]);
                },
            ]);
    }


    public function quickButtons(): array
    {
        $scope = input(\Poppy\MgrPage\Classes\Grid\Filter\Scope::QUERY_NAME);
        return [
            new BaseButton('<i class="fa fa-ban"></i> 封禁管理', route_url('py-mgr-page:backend.ban.index', null, [\Poppy\MgrPage\Classes\Grid\Filter\Scope::QUERY_NAME => $scope]), [
                'title' => "封禁管理",
                'class' => 'layui-btn layui-btn-sm',
            ]),
            new BaseButton('<i class="fa fa-map-signs"></i> 登录用户', route('py-mgr-page:backend.pam.token'), [
                'title' => "登录用户管理",
                'class' => 'layui-btn layui-btn-sm',
            ]),
            new BaseButton('<i class="fa fa-plus"></i> 新增', route_url('py-mgr-page:backend.pam.establish', null, ['type' => $scope]), [
                'title' => "新增账号",
                'class' => 'J_iframe layui-btn layui-btn-sm',
            ]),
        ];
    }
}
