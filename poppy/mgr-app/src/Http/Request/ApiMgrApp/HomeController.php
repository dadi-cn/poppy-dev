<?php

namespace Poppy\MgrApp\Http\Request\ApiMgrApp;

use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Widgets\SettingWidget;
use function sys_cache;

/**
 * 用户
 */
class HomeController extends BackendController
{
    /**
     * Setting
     * @param string $path 地址
     */
    public function setting(string $path = 'poppy.mgr-app')
    {
        $Setting = new SettingWidget();
        return $Setting->resp($path);
    }

    public function clearCache()
    {
        sys_cache('py-core')->flush();
        sys_cache('py-system')->flush();
        $this->pyConsole()->call('poppy:optimize');
        return Resp::success('已清空缓存');
    }
}