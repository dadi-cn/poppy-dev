<?php

namespace Poppy\AliyunOss\Http\Request\Api\Backend;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Poppy\AliyunOss\Http\MgrApp\SettingAliyunOss;
use Poppy\Framework\Classes\Resp;
use Poppy\MgrPage\Http\Request\Backend\BackendController;

/**
 * Aliyun 上传配置
 */
class UploadController extends BackendController
{

    public function __construct()
    {
        parent::__construct();

        self::$permission = [
            'global' => 'backend:py-system.global.manage',
        ];
    }

    /**
     * 上传配置
     * @return array|JsonResponse|RedirectResponse|Response|Resp
     */
    public function store()
    {
        return (new SettingAliyunOss())->resp();
    }
}
