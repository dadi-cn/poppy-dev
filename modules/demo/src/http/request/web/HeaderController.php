<?php

namespace Demo\Http\Request\Web;

use Illuminate\Http\Request;
use Poppy\Framework\Application\ApiController;

/**
 * 内容生成器
 */
class HeaderController extends ApiController
{

    public function index(Request $request)
    {
        dump($request->header());
    }

}