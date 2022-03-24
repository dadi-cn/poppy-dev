<?php

namespace Poppy\MgrApp\Http\Request\Api\Backend;

use Poppy\Core\Classes\Traits\CoreTrait;
use Poppy\Core\Exceptions\PermissionException;
use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Http\Form\FormPassword;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\Resources\PamResource;

/**
 * 用户
 */
class UserController extends BackendController
{
    use CoreTrait;

    /**
     * 主页
     * @throws PermissionException
     */
    public function info()
    {
        return Resp::success('获取成功', [
            'user'  => new PamResource($this->pam),
            'menus' => $this->coreModule()->path()->withPermission(PamAccount::TYPE_BACKEND, false, $this->pam)
        ]);
    }

    public function password()
    {
        $form = new FormPassword();
        return $form->resp();
    }
}