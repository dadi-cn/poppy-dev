<?php

namespace Poppy\Sms\Http\Request\Backend;

use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Poppy\MgrPage\Http\Request\Backend\BackendController;
use Poppy\Sms\Http\MgrPage\FormSettingAliyun;
use Poppy\Sms\Http\MgrPage\FormSettingChuanglan;

/**
 * 短信控制器
 */
class StoreController extends BackendController
{

    public function __construct()
    {
        parent::__construct();

        self::$permission = [
            'global' => 'backend:py-sms.global.manage',
        ];
    }

    /**
     * @return Factory|View
     */
    public function aliyun()
    {
        return (new FormSettingAliyun())->render();
    }

    /**
     * @return Factory|View
     */
    public function chuanglan()
    {
        return (new FormSettingChuanglan())->render();
    }

}