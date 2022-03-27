<?php

namespace Poppy\MgrPage\Http\Request\Backend;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrPage\Classes\Grid;
use Poppy\MgrPage\Http\MgrPage\FormBanEstablish;
use Poppy\MgrPage\Http\MgrPage\ListPamBan;
use Poppy\System\Action\Ban;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamBan;
use Poppy\System\Models\SysConfig;
use Response;
use Throwable;

class BanController extends BackendController
{
    /**
     * 设备
     * @throws ApplicationException
     * @throws Throwable
     */
    public function index()
    {
        $grid = new Grid(new PamBan());
        $grid->setLists(ListPamBan::class);
        return $grid->render();
    }


    public function status()
    {
        $type   = input('type');
        $key    = 'py-mgr-page::ban.status-' . $type;
        $status = sys_setting($key, SysConfig::NO);
        app('poppy.system.setting')->set($key, $status ? SysConfig::NO : SysConfig::YES);
        return Resp::success('已切换', '_reload|1');
    }

    public function type()
    {
        $type    = input('type');
        $key     = 'py-mgr-page::ban.type-' . $type;
        $isBlank = sys_setting($key, PamBan::WB_TYPE_BLACK) === PamBan::WB_TYPE_BLACK;
        app('poppy.system.setting')->set($key, $isBlank ? PamBan::WB_TYPE_WHITE : PamBan::WB_TYPE_BLACK);
        return Resp::success('已切换封禁模式', '_reload|1');
    }

    /**
     * 创建/编辑
     * @param null $id
     * @return array|JsonResponse|RedirectResponse|\Illuminate\Http\Response|Redirector|mixed|Resp|Response|string
     * @throws ApplicationException
     */
    public function establish($id = null)
    {
        $form = new FormBanEstablish();
        $form->setId($id);
        $form->setAccountType(input('type', PamAccount::TYPE_USER));
        return $form->render();
    }

    /**
     * 删除
     * @param $id
     * @return array|JsonResponse|RedirectResponse|\Illuminate\Http\Response|Redirector|Resp|Response
     */
    public function delete($id)
    {
        $Ban = new Ban();
        if (!$Ban->delete($id)) {
            return Resp::error($Ban->getError());
        }
        return Resp::success('删除成功', '_reload|1');
    }
}