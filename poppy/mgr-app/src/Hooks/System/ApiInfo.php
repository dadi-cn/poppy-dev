<?php

namespace Poppy\MgrApp\Hooks\System;

use Poppy\Core\Services\Contracts\ServiceArray;

class ApiInfo implements ServiceArray
{

    public function key(): string
    {
        return 'py-mgr-app';
    }

    public function data(): array
    {
        return [
            'auth_url'   => route('py-system:pam.auth.login'),
            'logout_url' => route('py-system:pam.auth.logout'),
            'info_url'   => route('py-mgr-app:api.user.info'),
        ];
    }
}