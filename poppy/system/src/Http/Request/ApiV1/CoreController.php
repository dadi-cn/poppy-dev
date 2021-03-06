<?php

namespace Poppy\System\Http\Request\ApiV1;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Poppy\Framework\Classes\Mocker;
use Poppy\Framework\Classes\Resp;
use Poppy\System\Action\Apidoc;
use Poppy\System\Classes\TestHelper;

/**
 * 系统信息控制
 */
class CoreController extends JwtApiController
{
    use ThrottlesLogins;

    /**
     * @api                   {post} api_v1/system/core/translate 多语言包
     * @apiVersion            1.0.0
     * @apiName               SysCoreTranslate
     * @apiGroup              Poppy
     */
    public function translate()
    {
        return Resp::success('翻译信息', [
            'json'         => true,
            'translations' => app('translator')->fetch('zh'),
        ]);
    }


    /**
     * @api                   {post} api_v1/system/core/info 系统信息
     * @apiVersion            1.0.0
     * @apiName               SysCoreInfo
     * @apiGroup              Poppy
     */
    public function info()
    {
        $hook   = sys_hook('poppy.system.api_info');
        $system = array_merge([], $hook);
        return Resp::success('获取系统配置信息', $system);
    }

    /**
     * @api                   {post} api_v1/system/core/doc 获取文档
     * @apiVersion            1.0.0
     * @apiName               SysCoreDoc
     * @apiGroup              Poppy
     * @apiQuery {string}     type 文档类型 [web:前端]
     */
    public function doc()
    {
        $type = input('type', 'web');
        $doc  = new Apidoc();
        if ($content = $doc->local($type)) {
            return Resp::success('获取文档信息', [
                'content' => $content
            ]);
        }

        return Resp::error($doc->getError());
    }

    /**
     * @api                   {post} api_v1/system/core/mock Mock
     * @apiVersion            1.0.0
     * @apiName               SysCoreMock
     * @apiGroup              Poppy
     * @apiQuery {string}     mock   Json 格式的数据
     */
    public function mock()
    {
        $data = Mocker::generate(input('mock'), 'zh_CN');
        return Resp::success('Success', $data);
    }

    /**
     * @api                    {post} api_v1/system/core/helper 帮助
     * @apiVersion             1.0.0
     * @apiName                SysCoreHelper
     * @apiGroup               Poppy
     * @apiQuery {string}      type   标识
     */
    public function helper()
    {
        $type = input('type');
        $data = TestHelper::generate($type);
        return Resp::success('Success', $data);
    }
}