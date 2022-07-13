<?php

namespace Poppy\Version\Http\Request\ApiV1\Web;

use Poppy\Framework\Classes\Resp;
use Poppy\System\Http\Request\ApiV1\Web\WebApiController;
use Poppy\Version\Models\SysAppVersion;

class VersionController extends WebApiController
{
    /**
     * @api                     {get} api_v1/version/app/version [Version]版本检测
     * @apiVersion              1.0.0
     * @apiName                 VersionAppVersion
     * @apiGroup                Poppy
     * @apiQuery    {string}    version       版本号
     * @apiSuccess  {string}    download_url  下载地址
     * @apiSuccess  {string}    description   描述
     * @apiSuccess  {string}    version       版本
     * @apiSuccess  {string}    is_upgrade    是否需要强制更新
     * @apiSuccessExample  data
     *  {
     *     "download_url": "http://www.1daolian.com",
     *     "description": "android",
     *     "version": "1.13.0",
     *     "is_upgrade": "Y"
     *  }
     */
    public function version()
    {
        $input   = input();
        $current = sys_get($input, 'version', '1.0.0');

        $os = (x_header('os') ?: x_app('os')) ?: 'android';

        if (!SysAppVersion::kvType($os, true)) {
            return Resp::error('不正确的平台信息');
        }
        $latestVersion = SysAppVersion::latestVersion($os);

        if (empty($latestVersion)) {
            return Resp::error('当前已是最新版本!');
        }

        if (version_compare($current, $latestVersion['title'], '>=')) {
            return Resp::error('您当前的版本是最新版本');
        }

        $isUpgrade = SysAppVersion::isUpgrade($os, $current);
        return Resp::success('获取版本成功', [
            'download_url' => sys_get($latestVersion, 'download_url'),
            'description'  => sys_get($latestVersion, 'description'),
            'version'      => sys_get($latestVersion, 'title'),
            'is_upgrade'   => $isUpgrade ? 'Y' : 'N',
        ]);
    }
}