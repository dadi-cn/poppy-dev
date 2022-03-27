<?php

namespace Poppy\Area\Http\Request\Backend;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Poppy\Area\Action\Area;
use Poppy\Area\Http\MgrPage\FormAreaEstablish;
use Poppy\Area\Http\MgrPage\ListArea;
use Poppy\Area\Models\SysArea;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrPage\Classes\Grid;
use Poppy\MgrPage\Http\Request\Backend\BackendController;
use Response;
use Throwable;

/**
 * 地区管理控制器
 */
class ContentController extends BackendController
{

    public function __construct()
    {
        parent::__construct();
        self::$permission = [
            'global' => 'backend:py-area.main.manage',
        ];
    }

    /**
     * 地区列表
     * @return array|JsonResponse|RedirectResponse|\Illuminate\Http\Response|Redirector|Resp|Response|string
     * @throws ApplicationException
     * @throws Throwable
     */
    public function index()
    {
        $grid = new Grid(new SysArea());
        $grid->setLists(ListArea::class);
        return $grid->render();
    }

    /**
     * 地区添加/编辑
     * @param null|int $id 地区id
     */
    public function establish($id = null)
    {
        $form = new FormAreaEstablish();
        $form->setPam($this->pam);
        $form->setId($id);
        return $form->render();
    }

    /**
     * 删除地区
     * @param int $id 地区id
     * @throws Exception
     */
    public function delete($id)
    {
        $Area = $this->action();
        if ($Area->delete($id)) {
            return Resp::success('删除成功', '_reload|1');
        }

        return Resp::error($Area->getError());
    }

    /**
     * 更新
     */
    public function fix()
    {
        return (new Area())->fixHandle();
    }

    /**
     * 版本Action
     * @return Area
     */
    private function action(): Area
    {
        return (new Area())->setPam($this->pam);
    }
}