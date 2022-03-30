<?php

namespace Poppy\MgrApp\Http\Request\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrApp\Classes\Grid\Filter\Query\Scope;
use Poppy\MgrApp\Classes\Widgets\GridWidget;
use Poppy\MgrApp\Http\MgrApp\FormBanEstablish;
use Poppy\MgrApp\Http\MgrApp\GridPamBan;
use Poppy\System\Action\Ban;
use Poppy\System\Models\PamBan;
use Poppy\System\Models\SysConfig;
use Throwable;
use function app;
use function input;
use function sys_setting;

class BanController extends BackendController
{
    /**
     * 设备
     * @throws ApplicationException
     * @throws Throwable
     */
    public function index()
    {
        $grid = new GridWidget(new PamBan());
        $grid->setLists(GridPamBan::class);
        return $grid->resp();
    }


    public function status()
    {
        $type   = input(Scope::QUERY_NAME);
        $key    = 'py-mgr-page::ban.status-' . $type;
        $status = sys_setting($key, SysConfig::NO);
        app('poppy.system.setting')->set($key, $status ? SysConfig::NO : SysConfig::YES);
        return Resp::success('已切换', 'motion|grid:filter');
    }

    public function type()
    {
        $type    = input(Scope::QUERY_NAME);
        $key     = 'py-mgr-page::ban.type-' . $type;
        $isBlank = sys_setting($key, PamBan::WB_TYPE_BLACK) === PamBan::WB_TYPE_BLACK;
        app('poppy.system.setting')->set($key, $isBlank ? PamBan::WB_TYPE_WHITE : PamBan::WB_TYPE_BLACK);
        return Resp::success('已切换封禁模式', 'motion|grid:filter');
    }

    /**
     * 创建/编辑
     * @return array|Response|JsonResponse|RedirectResponse|Resp
     */
    public function establish()
    {
        $form = new FormBanEstablish();
        return $form->resp();
    }

    /**
     * 删除
     * @param $id
     * @return JsonResponse|RedirectResponse|Response|Resp
     */
    public function delete($id)
    {
        $Ban = new Ban();
        if (!$Ban->delete($id)) {
            return Resp::error($Ban->getError());
        }
        return Resp::success('删除成功', 'motion|grid:reload');
    }
}