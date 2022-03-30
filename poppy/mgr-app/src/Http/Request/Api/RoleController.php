<?php

namespace Poppy\MgrApp\Http\Request\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrApp\Classes\Widgets\GridWidget;
use Poppy\MgrApp\Http\MgrApp\FormRoleEstablish;
use Poppy\MgrApp\Http\MgrApp\FormRolePermission;
use Poppy\MgrApp\Http\MgrApp\GridPamRole;
use Poppy\System\Action\Role;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;
use Throwable;
use View;

/**
 * 角色管理控制器
 */
class RoleController extends BackendController
{

    public function __construct()
    {
        parent::__construct();
        $types = PamAccount::kvType();
        View::share(compact('types'));

        self::$permission = [
            'global' => 'backend:py-system.role.manage',
            'delete' => 'backend:py-system.role.delete',
            'menu'   => 'backend:py-system.role.permissions',
        ];
    }

    /**
     * Display a listing of the resource.
     * @throws ApplicationException
     * @throws Throwable
     */
    public function index()
    {
        $grid = new GridWidget(new PamRole());
        $grid->setLists(GridPamRole::class);
        return $grid->resp();
    }

    /**
     * 编辑 / 创建
     */
    public function establish()
    {
        $form = new FormRoleEstablish();
        return $form->resp();
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id 角色id
     * @return JsonResponse|RedirectResponse|Resp|Response
     */
    public function delete($id)
    {
        $role = (new Role())->setPam($this->pam);
        if (!$role->delete($id)) {
            return Resp::error($role->getError());
        }

        return Resp::success('删除成功', 'motion|grid:reload');
    }

    /**
     * 菜单列表
     * @return JsonResponse|RedirectResponse|Resp|Response
     */
    public function menu()
    {
        $form = new FormRolePermission();
        return $form->resp();
    }
}