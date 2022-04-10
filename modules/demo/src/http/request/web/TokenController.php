<?php

namespace Demo\Http\Request\Web;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Poppy\Framework\Classes\Resp;
use Poppy\System\Http\Request\ApiV1\JwtApiController;

/**
 * 内容生成器
 */
class TokenController extends JwtApiController
{

    /**
     * @return JsonResponse|RedirectResponse|Response
     */
    public function index()
    {
        $this->pam();
        return Resp::success('ok');
    }
}