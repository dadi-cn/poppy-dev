<?php

namespace Poppy\System\Http\Middlewares;

use Closure;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Helper\EnvHelper;
use Poppy\System\Action\Ban as ActBan;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamBan;
use Poppy\System\Models\PamRole;
use Poppy\System\Models\SysConfig;

/**
 * 禁止访问, 对于用户访问的控制
 * 如果是前台用户, 放到所有请求之前
 * 如果是后台用户, 放到所有请求之后(需要放过管理员)
 */
class Ban
{

    /**
     * @param         $request
     * @param Closure $next
     * @param string $type 账号类型, 用于封禁
     * @return mixed
     */
    public function handle($request, Closure $next, string $type = 'user')
    {
        //获取ip
        $ip = EnvHelper::ip();

        if ($appType = x_header('type')) {
            $type = $appType;

        }

        $status  = sys_setting('py-mgr-page::ban.status-' . $type, SysConfig::DISABLE);
        $isBlack = sys_setting('py-mgr-page::ban.type-' . $type, PamBan::WB_TYPE_BLACK) === PamBan::WB_TYPE_BLACK;

        /* 未开启风险拦截
         * ---------------------------------------- */
        if (!$status) {
             return $next($request);
        }
        // 是否是root用户 不进行拦截
        if ($type === PamAccount::TYPE_BACKEND && $user = app('auth')->guard()->user()) {
            /** @var PamAccount $user */
            if ($user->roles->where('name', PamRole::BE_ROOT)->count()) {
                return $next($request);
            }
        }
        $Ban  = new ActBan();
        $ipIn = $Ban->checkIn($type, PamBan::TYPE_IP, $ip);

        /* 黑名单策略, 黑名单, Ip In : 封禁
         * ---------------------------------------- */
        if ($isBlack && $ipIn) {
            return Resp::error("当前 ip '{$ip}' 被封禁，请联系客服处理");
        }

        /* 白名单策略, 不在列表内, 提示
         * ---------------------------------------- */
        if (!$isBlack && !$ipIn) {
            return Resp::error("当前ip '{$ip}' 不允许访问，请联系客服处理");
        }


        $deviceId = x_header('id') ?: input('device_id');
        if ($deviceId) {
            $deviceIn = $Ban->checkIn($type, PamBan::TYPE_DEVICE, $deviceId);
            /* 黑名单策略, 设备In : 封禁
             * ---------------------------------------- */
            if ($isBlack && $deviceIn) {
                return Resp::error('当前设备被封禁，请联系客服处理');
            }

            /* 白名单策略, 设备不在列表中, 封禁
             * ---------------------------------------- */
            if (!$isBlack && !$deviceIn) {
                return Resp::error('当前设备不允许访问系统, 请联系管理员');
            }
        }

        return $next($request);
    }
}