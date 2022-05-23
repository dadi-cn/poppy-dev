<?php

namespace Poppy\AliyunOss\Http\Request\ApiV1\Web;

use Poppy\AliyunOss\Action\Sts;
use Poppy\Framework\Classes\Resp;
use Poppy\System\Http\Request\ApiV1\JwtApiController;

/**
 * Sts 配置
 */
class StsController extends JwtApiController
{

    /**
     * @api                   {post} api_v1/aliyun-oss/sts/temp_oss AliOss Sts授权
     * @apiDescription        命名规则采用 His{rand(8)} 格式
     * @apiVersion            1.0.0
     * @apiName               AliyunOssStsTempOss
     * @apiGroup              Poppy
     * @apiSuccess {object[]} data              返回
     * @apiSuccess {string}   directory         允许上传的目录
     * @apiSuccess {string}   prefix_url        组合上传的URL
     * @apiSuccess {string}   bucket            存储名称
     * @apiSuccess {string}   access_key_secret key
     * @apiSuccess {string}   access_key_id     id
     * @apiSuccess {string}   expiration        过期时间
     * @apiSuccess {string}   security_token    安全token
     * @apiSuccessExample     data
     * {
     *     "status": 0,
     *     "message": "",
     *     "data": {
     *         "directory": "upload/202106/02/",
     *         "prefix_url": "https://test-oss.domain.com",
     *         "bucket": "dadi-xxx",
     *         "endpoint": "oss-cn-beijing.aliyuncs.com",
     *         "security_token": "CAISqQJ1q6Ft5B2yfSjIr5fAB+....veLex67A==",
     *         "access_key_id": "STS.NTu...xC",
     *         "access_key_secret": "2sKKB5cg9...JEJR2p",
     *         "expiration": "2021-06-02T02:51:45Z"
     *     }
     * }
     */
    public function tempOss()
    {
        if (sys_setting('py-system::picture.save_type') !== 'aliyun') {
            return Resp::error('后台配置必须开启 Aliyun 存储');
        }
        $config = config('poppy.aliyun-oss');
        $Sts    = new Sts();
        $Sts->setConfig($config['temp_key'], $config['temp_secret'], $config['bucket'], $config['endpoint'], $config['role_arn'], $config['url']);
        if ($Sts->tempOss()) {
            return Resp::web(Resp::SUCCESS, '获取成功', $Sts->getTempKey());
        }

        return Resp::web(Resp::ERROR, $Sts->getError());
    }
}
