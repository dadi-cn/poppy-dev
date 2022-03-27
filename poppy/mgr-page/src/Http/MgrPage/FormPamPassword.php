<?php

namespace Poppy\MgrPage\Http\MgrPage;

use Illuminate\Support\Facades\Validator;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrPage\Classes\Widgets\FormWidget;
use Poppy\System\Action\Pam;
use Poppy\System\Models\PamAccount;
use function input;
use function is_post;
use function request;
use function sys_is_demo;

class FormPamPassword extends FormWidget
{
    public $ajax = true;

    private $id;

    /**
     * @var PamAccount
     */
    private $pam;

    /**
     * @param $id
     * @return $this
     * @throws ApplicationException
     */
    public function setId($id)
    {
        $this->id = $id;
        if ($id) {
            $this->pam = PamAccount::find($this->id);

            if (!$this->pam) {
                throw  new ApplicationException('无用户数据');
            }

        }
        return $this;
    }

    public function handle()
    {
        $id = input('id');
        if (is_post()) {
            $this->setId($id);
            $validator = Validator::make(input(), [
                'password' => [
                    Rule::required(),
                    Rule::confirmed(),
                ],
            ], []);
            if ($validator->fails()) {
                return Resp::error($validator->errors());
            }

            $password = input('password');

            $actPam = new Pam();
            $actPam->setPam(request()->user());
            if (sys_is_demo()) {
                return Resp::error('演示模式下无法修改密码');
            }
            if ($actPam->setPassword($this->pam, $password)) {
                return Resp::success('设置密码成功', '_top_reload|1');
            }

            return Resp::error($actPam->getError());
        }

    }

    public function data(): array
    {
        if ($this->id) {
            return [
                'id'       => $this->pam->id,
                'username' => $this->pam->username,
            ];
        }
        return [];
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        if ($this->id) {
            $this->hidden('id', 'ID');
        }
        $this->text('username', '用户名')->disable();
        $this->password('password', '密码');
        $this->password('password_confirmation', '重复密码');
    }
}
