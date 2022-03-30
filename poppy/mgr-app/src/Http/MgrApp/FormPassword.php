<?php

namespace Poppy\MgrApp\Http\MgrApp;

use Auth;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Widgets\FormWidget;
use Poppy\System\Action\Pam;
use Poppy\System\Classes\Contracts\PasswordContract;
use Poppy\System\Classes\Traits\PamTrait;
use Poppy\System\Models\PamAccount;
use function app;
use function data_get;
use function input;
use function is_post;
use function sys_is_demo;

class FormPassword extends FormWidget
{

    use PamTrait;

    protected string $title = '修改密码';

    public function handle()
    {
        if (is_post()) {
            $old_password = input('old_password');
            $password     = input('password');

            $Pam = new Pam();
            /** @var PamAccount $pam */
            $pam = Auth::user();
            if (!app(PasswordContract::class)->check($pam, $old_password)) {
                return Resp::error('原密码错误!');
            }

            if (sys_is_demo()) {
                return Resp::error('演示模式下无法修改密码');
            }

            if (!$Pam->setPassword($pam, $password)) {
                return Resp::error($Pam->getError());
            }
            return Resp::success('密码修改成功, 请重新登录', 'motion|reload;time|3000');
        }
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
        $this->password('old_password', '原密码')->rules([
            Rule::required(),
        ]);
        $this->password('password', '密码')->rules([
            Rule::required(),
            Rule::confirmed(),
        ]);
        $this->password('password_confirmation', '重复密码')->rules([
            Rule::required(),
        ]);
    }
}
