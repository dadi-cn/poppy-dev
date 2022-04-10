<?php

namespace Poppy\System\Http\Request\ApiV1;

use Illuminate\Contracts\Auth\Authenticatable;
use Poppy\Framework\Application\ApiController;
use Poppy\System\Models\PamAccount;

/**
 * 后台用户获取
 */
abstract class BackendApiController extends ApiController
{

    /**
     * @var ?PamAccount
     */
    protected ?PamAccount $pam = null;

    public function __construct()
    {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            $this->pam = $request->user();
            return $next($request);
        });
    }

    /**
     * 返回 Jwt 用户
     * @return Authenticatable|PamAccount
     * @see        $pam
     */
    protected function pam()
    {
        if ($this->pam) {
            return $this->pam;
        }
        $this->pam = app('request')->user(PamAccount::GUARD_JWT_BACKEND);
        if (!$this->pam) {
            $this->pam = app('auth')->guard(PamAccount::GUARD_JWT_BACKEND)->user();
        }

        return $this->pam;
    }
}