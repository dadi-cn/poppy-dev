<?php

namespace Poppy\MgrPage\Http\Request\Backend;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrPage\Classes\Grid;
use Poppy\MgrPage\Classes\Layout\Content;
use Poppy\MgrPage\Http\MgrPage\FormPamDisable;
use Poppy\MgrPage\Http\MgrPage\FormPamEnable;
use Poppy\MgrPage\Http\MgrPage\FormPamEstablish;
use Poppy\MgrPage\Http\MgrPage\FormPamPassword;
use Poppy\MgrPage\Http\MgrPage\ListPamAccount;
use Poppy\MgrPage\Http\MgrPage\ListPamLog;
use Poppy\MgrPage\Http\MgrPage\ListPamToken;
use Poppy\System\Action\Ban;
use Poppy\System\Events\PamTokenBanEvent;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamLog;
use Poppy\System\Models\PamToken;
use Response;
use Throwable;

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
     * @throws ApplicationException|Throwable
     */
    public function index()
    {
        $grid = new Grid(new PamAccount());
        $grid->setLists(ListPamAccount::class);
        return $grid->render();
    }

    /**
     * Show the form for creating a new resource.
     * @param null|int $id ID
     * @throws Throwable
     */
    public function establish($id = null)
    {
        $form = new FormPamEstablish();
        if (!$id) {
            $form->setType((string) input('type'));
        }
        else {
            $form->setId($id);
        }
        return $form->render();
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
        return $form->render();
    }

    /**
     * 禁用用户
     * @param int $id 用户ID
     */
    public function disable($id)
    {
        $form = new FormPamDisable();
        $form->setId($id);
        return $form->render();
    }

    /**
     * 启用用户
     * @param int $id 用户ID
     * @return Content
     */
    public function enable($id)
    {
        $form = new FormPamEnable();
        $form->setId($id);
        return $form->render();
    }

    /**
     * @return array|\Illuminate\Http\Response|JsonResponse|Redirector|RedirectResponse|Resp|Response
     * @throws ApplicationException
     * @throws Throwable
     */
    public function log()
    {
        $grid = new Grid(new PamLog());
        $grid->setLists(ListPamLog::class);
        return $grid->render();
    }

    /**
     * @return array|\Illuminate\Http\Response|JsonResponse|Redirector|RedirectResponse|Resp|Response
     * @throws ApplicationException
     * @throws Throwable
     */
    public function token()
    {
        $grid = new Grid(new PamToken());
        $grid->setLists(ListPamToken::class);
        return $grid->render();
    }

    public function ban($id, $type)
    {
        $Ban = new Ban();
        if (!$Ban->type($id, $type)) {
            return Resp::error($Ban->getError());
        }
        return Resp::success('禁用成功', '_top_reload|1');
    }

    public function deleteToken($id)
    {
        $item = PamToken::find($id);

        // 踢下线(当前用户不可访问)
        $Ban = new Ban();
        $Ban->forbidden($item->account_id);
        $item->delete();

        event(new PamTokenBanEvent($item, 'token'));
        return Resp::error('删除用户成功, 用户已无法访问(需重新登录)', '_top_reload|1');
    }
}