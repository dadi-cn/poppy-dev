<?php

namespace Poppy\System\Events;

use Poppy\System\Models\PamAccount;

/**
 * 登录成功事件
 */
class LoginSuccessEvent
{
    /**
     * @var PamAccount 用户账户
     */
    public PamAccount $pam;

    /**
     * @var string
     */
    public string $guard;

    public function __construct(PamAccount $pam, $guard)
    {
        $this->pam   = $pam;
        $this->guard = $guard;
    }
}