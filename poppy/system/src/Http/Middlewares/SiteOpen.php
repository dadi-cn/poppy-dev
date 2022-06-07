<?php

namespace Poppy\System\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Poppy\Framework\Classes\Resp;

/**
 * 网站开启/关闭, 用户需要限制, 其他人不进行限制
 */
class SiteOpen
{

    /**
     * Handle an incoming request.
     * @param Request $request 请求
     * @param Closure $next    后续处理
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $type = x_header('type') ?? 'user';
        if (!sys_setting('py-system::site.is_open') && $type === 'user') {
            $reason = sys_setting('py-system::site.close_reason');
            return Resp::error('网站临时关闭, 原因:' . $reason);
        }

        return $next($request);
    }
}