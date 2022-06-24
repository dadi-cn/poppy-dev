<?php

namespace Poppy\MgrApp\Http\Request\ApiMgrApp;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Widgets\GridWidget;
use Poppy\MgrApp\Http\MgrApp\FormPamDisable;
use Poppy\MgrApp\Http\MgrApp\FormPamEnable;
use Poppy\MgrApp\Http\MgrApp\FormPamEstablish;
use Poppy\MgrApp\Http\MgrApp\FormPamPassword;
use Poppy\MgrApp\Http\MgrApp\GridPamAccount;
use Poppy\MgrApp\Http\MgrApp\GridPamLog;
use Poppy\MgrApp\Http\MgrApp\GridPamToken;
use Poppy\System\Action\Ban;
use Poppy\System\Events\PamTokenBanEvent;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamLog;
use Poppy\System\Models\PamToken;
use Throwable;
use function event;

/**
 * 账户管理
 */
class PamController extends BackendController
{
    public function __construct()
    {
        parent::__construct();

        self::$permission = [
            'global' => 'backend:py-system.pam.manage',
            'log'    => 'backend:py-system.pam.log',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $grid = new GridWidget(new PamAccount());
        $grid->setLists(GridPamAccount::class);
        return $grid->resp();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function establish()
    {
        $form = new FormPamEstablish();
        return $form->resp();
    }

    /**
     * 设置密码
     * @param int $id 用户ID
     * @throws Throwable
     */
    public function password(int $id)
    {
        $form = new FormPamPassword();
        $form->setId($id);
        return $form->resp();
    }

    /**
     * 禁用用户
     */
    public function disable()
    {
        $form = new FormPamDisable();
        return $form->resp();
    }

    /**
     * 启用用户
     */
    public function enable()
    {
        $form = new FormPamEnable();
        return $form->resp();
    }

    /**
     * @return Response|JsonResponse|RedirectResponse
     */
    public function log()
    {
        $grid = new GridWidget(new PamLog());
        $grid->setLists(GridPamLog::class);
        return $grid->resp();
    }

    /**
     * @return Response|JsonResponse|RedirectResponse
     * @throws Throwable
     */
    public function token()
    {
        $grid = new GridWidget(new PamToken());
        $grid->setLists(GridPamToken::class);
        return $grid->resp();
    }

    public function ban($id, $type)
    {
        $Ban = new Ban();
        if (!$Ban->type($id, $type)) {
            return Resp::error($Ban->getError());
        }
        return Resp::success('禁用成功', 'motion|grid:reload');
    }

    public function deleteToken($id)
    {
        $item = PamToken::find($id);

        // 踢下线(当前用户不可访问)
        $Ban = new Ban();
        $Ban->forbidden($item->account_id);
        $item->delete();

        event(new PamTokenBanEvent($item, 'token'));
        return Resp::success('删除用户成功, 用户已无法访问(需重新登录)', 'motion|grid:reload');
    }
}