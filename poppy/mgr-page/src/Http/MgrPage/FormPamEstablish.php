<?php

namespace Poppy\MgrPage\Http\MgrPage;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrPage\Classes\Widgets\FormWidget;
use Poppy\System\Action\Pam;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;
use function input;

class FormPamEstablish extends FormWidget
{

    public $ajax = true;

    private $type;

    private $id;

    /**
     * @var PamAccount
     */
    private $item;

    /**
     * 设置id
     * @param $id
     * @return $this
     */
    public function setId($id): self
    {
        $this->id = $id;

        if ($id) {
            $this->item = PamAccount::find($this->id);
            if ($this->item) {
                $this->type = $this->item->type;
            }
        }
        return $this;
    }

    /**
     * 设置类型
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function handle()
    {
        $username = input('username');
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
            return Resp::success('用户修改成功', [
                '_top_reload' => 1,
            ]);
        }

        $Pam = new Pam();
        if ($Pam->register($username, $password, $role_id)) {
            return Resp::success('用户添加成功', [
                '_top_reload' => 1,
                'id'          => $Pam->getPam()->id,
            ]);
        }
        return Resp::error($Pam->getError());
    }

    public function data(): array
    {
        if ($this->item) {
            return [
                'id'       => $this->item->id,
                'username' => $this->item->username,
                'role_id'  => $this->item->roles->pluck('id')->toArray(),
            ];
        }
        return [];
    }

    public function form()
    {
        if ($this->id) {
            $this->hidden('id', 'ID');
            $this->text('username', '用户名')->readonly()->disable();
            $this->tags('role_id', '用户角色')->options(PamRole::getLinear($this->type))->readonly();

        }
        else {
            $this->text('username', '用户名')->rules([
                Rule::nullable(),
            ]);
            $this->tags('role_id', '用户角色')->options(PamRole::getLinear($this->type));
        }

        $this->password('password', '密码');
        $this->hidden('type', $this->type)->default($this->type);
    }
}
