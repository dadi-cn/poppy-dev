<?php

namespace Poppy\System\Listeners\LoginSuccess;

use Illuminate\Support\Str;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\System\Events\LoginSuccessEvent;
use Poppy\System\Http\Middlewares\AuthenticateSession;

/**
 * 登录成功更新登录次数 + 最后登录时间
 */
class UpdatePasswordHashListener
{
    use PoppyTrait;

    /**
     * @param LoginSuccessEvent $event 登录成功
     */
    public function handle(LoginSuccessEvent $event)
    {
        $name = $event->guard;
        if ($name && !Str::contains($name, 'jwt')) {
            $hashKey = AuthenticateSession::hashKey($name);
            $this->pySession()->put($hashKey, $event->pam->getAuthPassword());
        }
    }
}

