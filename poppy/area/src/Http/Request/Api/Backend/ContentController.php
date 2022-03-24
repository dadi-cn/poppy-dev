<?php

namespace Poppy\Area\Http\Request\Api\Backend;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Poppy\Area\Action\Area;
use Poppy\Area\Http\Form\FormAreaEstablish;
use Poppy\Area\Http\Grid\GridArea;
use Poppy\Area\Models\SysArea;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrApp\Classes\Widgets\GridWidget;
use Poppy\MgrPage\Http\Request\Backend\BackendController;
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
     * @return Response|JsonResponse|RedirectResponse|Resp
     * @throws ApplicationException
     * @throws Throwable
     */
    public function index()
    {
        $grid = new GridWidget(new SysArea());
        $grid->setLists(GridArea::class);
        return $grid->resp();
    }

    /**
     * 地区添加/编辑
     */
    public function establish()
    {
        $form = new FormAreaEstablish();
        return $form->resp();
    }

    /**
     * 删除地区
     * @param int $id 地区id
     * @throws Exception
     */
    public function delete(int $id)
    {
        $Area = $this->action();
        if ($Area->delete($id)) {
            return Resp::success('删除成功', 'motion|grid:reload');
        }

        return Resp::error($Area->getError());
    }

    /**
     * 更新
     */
    public function fix()
    {
        return (new Area())->fixHandle(false);
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