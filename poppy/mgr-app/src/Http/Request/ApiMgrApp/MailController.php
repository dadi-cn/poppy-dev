<?php

namespace Poppy\MgrApp\Http\Request\ApiMgrApp;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Http\MgrApp\FormMailTest;
use Poppy\MgrApp\Http\MgrApp\SettingMail;
use Poppy\System\Classes\Traits\SystemTrait;

/**
 * 邮件控制器
 */
class MailController extends BackendController
{
    use SystemTrait;

    public function __construct()
    {
        parent::__construct();

        self::$permission = [
            'global' => 'backend:py-system.global.manage',
        ];
    }

    /**
     * 保存邮件配置
     * @return JsonResponse|RedirectResponse|Resp|Response
     */
    public function store()
    {
        return (new SettingMail())->resp();
    }

    /**
     * 测试邮件发送
     */
    public function test()
    {
        return (new FormMailTest())->resp();
    }
}
