<?php

namespace Poppy\MgrApp\Http\MgrApp;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Filter\Query\Scope;
use Poppy\MgrApp\Classes\Widgets\FormWidget;
use Poppy\System\Action\Pam;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;
use Throwable;

class FormPamEstablish extends FormWidget
{

    protected string $title = '账号添加';

    private string $type;

    private int $id;

    /**
     * @var PamAccount
     */
    private $item;

    public function __construct()
    {
        parent::__construct();
        $this->type = (string) input(Scope::QUERY_NAME);
        $this->id   = (int) app('router')->current()->parameter('id');
        if ($this->id) {
            $this->title = '账号编辑';
            $this->item  = PamAccount::findOrFail($this->id);
            $this->type  = $this->item->type;
        }
    }


    /**
     * @throws Throwable
     */
    public function handle()
    {
        $password = input('password');
        $role_id  = input('role_id');

        if (!$role_id) {
            return Resp::error('请选择角色');
        }
        if ($this->item) {
            $Pam = new Pam();
            if ($password) {
                if (!$Pam->setPassword($this->item, $password)) {
                    return Resp::error($Pam->getError());
                }
            }
            $Pam->setRoles($this->item, $role_id);
            $mobile   = input('mobile');
            $email    = input('email');
            $username = input('username');
            if ($mobile && $mobile !== $this->item->mobile) {
                if (!$Pam->rebind($this->item->mobile, $mobile)) {
                    return Resp::error($Pam->getError());
                }
            }
            if ($email && $email !== $this->item->email) {
                if (!$Pam->rebind($this->item->email, $email)) {
                    return Resp::error($Pam->getError());
                }
            }
            if ($username && $username !== $this->item->username) {
                if (!$Pam->rebind($this->item->username, $username)) {
                    return Resp::error($Pam->getError());
                }
            }
            return Resp::success('用户修改成功', 'motion|grid:reload');
        }

        $passport = input('passport');
        if ($passport) {
            $Pam = new Pam();
            if ($Pam->register($passport, $password, $role_id)) {
                return Resp::success('用户添加成功', 'motion|grid:reload');
            } else {
                return Resp::error($Pam->getError());
            }
        }

        return Resp::error('错误的数据输入');
    }

    public function data(): array
    {
        if ($this->item) {
            return [
                'id'       => $this->item->id,
                'username' => $this->item->username,
                'mobile'   => $this->item->mobile,
                'email'    => $this->item->email,
                'role_id'  => $this->item->roles->pluck('id')->toArray(),
            ];
        }
        return [];
    }

    public function form()
    {
        if ($this->id) {
            $this->text('username', '用户名')->rules([
                Rule::username()
            ]);
            $this->text('mobile', '手机号')->rules([
                Rule::mobile()
            ]);
            $this->text('email', '邮箱')->rules([
                Rule::email()
            ]);

        } else {
            $this->text('passport', '通行证')->rules([
                Rule::nullable(),
            ])->help('支持用户名/手机号/邮箱');
        }
        $this->tags('role_id', '角色')->options(PamRole::getLinear($this->type));
        $this->password('password', '密码');
    }
}
