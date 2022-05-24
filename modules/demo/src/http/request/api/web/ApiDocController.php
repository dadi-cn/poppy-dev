<?php

namespace Demo\Http\Request\Api\Web;

use Poppy\Framework\Application\ApiController;
use Poppy\Framework\Classes\Resp;

class ApiDocController extends ApiController
{
    /**
     * @api               {get} api/demo/apidoc/how [Demo]ApiDoc编写示例
     * @apiDescription    怎样写Apidoc
     * @apiVersion        1.0.0
     * @apiName           ApidocHow
     * @apiGroup          Demo
     * @apiQuery {integer}            number         数值
     * @apiQuery {int{100-999}}   number_range   数值范围
     * @apiQuery {string}         string         字串
     * @apiQuery {string{..5}}    string_mx      字串最大5
     * @apiQuery {string{2..5}}   string_between 字串间隔
     * @apiQuery {int{2..5}}      number_between 数值间隔
     * @apiQuery {int=1,2,3,99}   number_select  数值间隔
     * @apiQuery {string=banana,apple,ball} string_select  字串枚举
     */
    public function how()
    {
        return Resp::success('返回输入值', input());
    }
}