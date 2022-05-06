<?php

namespace Demo\Http\Request\Api\Web;

use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Resp;

class RespController extends Controller
{

    /**
     * @api                    {get} api/demo/resp/success   [Demo]Resp-Success
     * @apiDescription         接口成功请求
     * @apiVersion             1.0.0
     * @apiName                RespSuccess
     * @apiGroup               Demo
     * @apiSuccessExample      return
     * {
     *     "status": 0,
     *     "message": "[开发]返回成功的信息"
     * }
     */
    public function success()
    {
        $location = input('location');
        $append   = [];
        if ($location) {
            // 使用 meta 方式立即跳转, 返回状态码是 200
            $append['_location'] = $location;
            $append['_time']     = false;
        }
        return Resp::success('返回成功的信息', $append);
    }

    /**
     * @api                    {get} api/demo/resp/error   [Demo]Resp-Error
     * @apiDescription         接口失败请求
     * @apiVersion             1.0.0
     * @apiName                RespError
     * @apiGroup               Demo
     * @apiSuccessExample      return
     * {
     *     "status": 1,
     *     "message": "[开发]返回错误提示"
     * }
     */
    public function error()
    {
        return Resp::error('返回错误提示');
    }


    /**
     * @api                    {get} api/demo/resp/validator   [Demo]Resp-Validator
     * @apiDescription         接口失败请求
     * @apiVersion             1.0.0
     * @apiName                RespValidator
     * @apiGroup               Demo
     */
    public function validator()
    {
        $validator = Validator::make([
            'user' => '',
            'my' => ''
        ], [
            'user' => [
                Rule::required()
            ],
            'my' => [
                Rule::required()
            ]
        ]);
        if ($validator->fails()) {
            return Resp::error($validator->messages());
        }
        return Resp::success('验证通过');
    }

    /**
     * @api                    {get} api/demo/resp/401   [Demo]Resp-401
     * @apiDescription         接口未授权请求
     * @apiVersion             1.0.0
     * @apiName                Resp401
     * @apiGroup               Demo
     */
    public function unAuth()
    {
        return response()->json([
            'message' => 'Token 错误',
            'status'  => 401,
        ], 401);
    }

    /**
     * @api                    {get} api/demo/resp/header   [Demo]Resp-Header
     * @apiVersion             1.0.0
     * @apiName                RespHeader
     * @apiGroup               Demo
     */
    public function header()
    {
        return Resp::success('访问成功', [
            'x-app-id'      => x_header('app-id'),
            'x-app-os'      => x_header('app-os'),
            'x-app-version' => x_header('app-version'),
        ]);
    }
}
