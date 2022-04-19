<?php

namespace Poppy\MgrApp\Http\Request\ApiMgrApp;

use Poppy\MgrApp\Http\MgrApp\FormMailTest;
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
     * 测试邮件发送
     */
    public function test()
    {
        return (new FormMailTest())->resp();
    }
}
