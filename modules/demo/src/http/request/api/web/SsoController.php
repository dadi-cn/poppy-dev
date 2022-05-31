<?php

namespace Demo\Http\Request\Api\Web;

use Poppy\Framework\Classes\Resp;
use Poppy\System\Http\Request\ApiV1\WebApiController;

class SsoController extends WebApiController
{

    /**
     * @api                    {get} api/demo/sso/access   [Demo]Sso-Access
     * @apiDescription         Sso 请求
     * @apiVersion             1.0.0
     * @apiName                SSoAccess
     * @apiGroup               Demo
     */
    public function access()
    {
        return Resp::success('通过 Sso 的登录用户的信息', [
            'id' => $this->pam->id,
        ]);
    }
}
