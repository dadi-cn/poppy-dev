<?php

namespace Poppy\System\Events;

use Poppy\System\Models\PamAccount;

/**
 * 用户注册事件
 */
class PamPasswordModifiedEvent
{
    /**
     * @var PamAccount
     */
    public PamAccount $pam;

    /**
     * PamRegisteredEvent constructor.
     * @param PamAccount $pam
     */
    public function __construct(PamAccount $pam)
    {
        $this->pam       = $pam;
    }
}