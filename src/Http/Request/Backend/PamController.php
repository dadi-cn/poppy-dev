<?php

namespace Poppy\MgrPage\Http\Request\Backend;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\System\Classes\Grid;
use Poppy\System\Classes\Layout\Content;
use Poppy\System\Http\Forms\Backend\FormPamDisable;
use Poppy\System\Http\Forms\Backend\FormPamEnable;
use Poppy\System\Http\Forms\Backend\FormPamEstablish;
use Poppy\System\Http\Forms\Backend\FormPamPassword;
use Poppy\System\Http\Lists\Backend\ListPamAccount;
use Poppy\System\Http\Lists\Backend\ListPamLog;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamLog;
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
        return (new Content())->body($grid->render());
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
     * @return array|JsonResponse|RedirectResponse|\Illuminate\Http\Response|Redirector|Resp|Content|Response
     * @throws ApplicationException
     * @throws Throwable
     */
    public function log()
    {
        $grid = new Grid(new PamLog());
        $grid->setLists(ListPamLog::class);
        return (new Content())->body($grid->render());
    }
}