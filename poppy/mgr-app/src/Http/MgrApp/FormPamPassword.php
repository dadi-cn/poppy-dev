<?php

namespace Poppy\MgrApp\Http\MgrApp;

use Illuminate\Support\Facades\Validator;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Validation\Rule;
use Poppy\System\Action\Pam;
use Poppy\MgrApp\Classes\Widgets\FormWidget;
use Poppy\System\Models\PamAccount;

class FormPamPassword extends FormWidget
{

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
                return Resp::success('设置密码成功', 'motion|reload');
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
        $this->text('username', '用户名')->disabled();
        $this->password('password', '密码');
        $this->password('password_confirmation', '重复密码');
    }
}
