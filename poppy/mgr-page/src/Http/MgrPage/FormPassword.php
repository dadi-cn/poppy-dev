<?php

namespace Poppy\MgrPage\Http\MgrPage;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrPage\Classes\Widgets\FormWidget;
use Poppy\System\Action\Pam;
use Poppy\System\Classes\Contracts\PasswordContract;
use Poppy\System\Classes\Traits\PamTrait;
use Poppy\System\Models\PamAccount;
use function app;
use function data_get;
use function input;
use function route;
use function sys_is_demo;

class FormPassword extends FormWidget
{

    use PamTrait;

    public $ajax = true;
    protected $title = '修改密码';

    public function handle()
    {

        $old_password = input('old_password');
        $password     = input('password');
        $id           = input('account_id');

        $Pam       = new Pam();
        $this->pam = PamAccount::find($id);
        if (!app(PasswordContract::class)->check($this->pam, $old_password)) {
            return Resp::error('原密码错误!');
        }

        if (sys_is_demo()) {
            return Resp::error('演示模式下无法修改密码');
        }

        if (!$Pam->setPassword($this->pam, $password)) {
            return Resp::error($Pam->getError());
        }
        app('auth')->guard(PamAccount::GUARD_BACKEND)->logout();

        return Resp::success('密码修改成功, 请重新登录', '_location|' . route('py-mgr-page:backend.home.login'));

    }

    public function data(): array
    {
        return [
            'account_id' => data_get($this->pam, 'id'),
        ];
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->hidden('account_id', 'account_id');
        $this->password('old_password', '原密码')->rules([
            Rule::required(),
        ]);
        $this->password('password', '密码')->rules([
            Rule::required(),
            Rule::confirmed(),
        ]);
        $this->password('password_confirmation', '重复密码')->rules([
            Rule::required(),
        ]);;
    }
}
