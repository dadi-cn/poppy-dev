<?php

namespace Poppy\AliyunOss\Action;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Poppy\Framework\Classes\Traits\AppTrait;

/**
 * Reviewed 阿里临时授权
 */
class Sts
{
    use AppTrait;

    /**
     * @var array 临时授权信息
     */
    protected array $tempKey;

    /**
     * @var mixed|string
     */
    private $url;

    /**
     * 子用户的key
     * @var string
     */
    private $tempAppKey;

    /**
     * 子用户的密钥
     * @var string
     */
    private $tempAppSecret;

    /**
     * @var string
     */
    private $bucket;

    /**
     * @var string
     */
    private $endpoint;

    /**
     * 角色资源描述符，在RAM的控制台的资源详情页上可以获取
     * @url https://ram.console.aliyun.com/#/role/list
     */
    private $roleArn;


    /**
     * 子目录
     * @var string
     */
    private $subDirectory = '';

    /**
     * @return array 获取临时授权
     */
    public function getTempKey(): array
    {
        return $this->tempKey;
    }

    public function setConfig($app_key, $app_secret, $bucket, $endpoint, $role_arn, $url_prefix = '')
    {
        $this->tempAppKey    = $app_key;
        $this->tempAppSecret = $app_secret;
        $this->bucket        = $bucket;
        $this->endpoint      = $endpoint;
        $this->roleArn       = $role_arn;
        $this->url           = $url_prefix;
    }

    public function setSubDirectory($directory = '')
    {
        $this->subDirectory = $directory;
    }

    /**
     * 返回 Ali 授权key
     * @return string
     */
    public function tempOss(): string
    {
        //加载aliyun配置
        $bucket = $this->bucket;

        $date = Carbon::now()->format('Ym');
        $day  = Carbon::now()->format('d');

        $subDirectory = $this->subDirectory ? $this->subDirectory . '/' : '';

        $dir = "upload/{$subDirectory}{$date}/{$day}/";

        // 在扮演角色(AssumeRole)时，可以附加一个授权策略，进一步限制角色的权限；
        // 详情请参考《RAM使用指南》
        // https://help.aliyun.com/document_detail/28664.html
        $policy = <<<POLICY
{
	"Version": "1",
	"Statement": [
		{
			"Effect": "Allow",
			"Action": [
				"oss:Put*",
				"oss:PutObject"
			],
			"Resource": [
				"acs:oss:*:*:$bucket/$dir*"
			]
		}
	]
}
POLICY;

        try {
            /**
             * https://api.aliyun.com/#/?product=Sts&version=2015-04-01&api=AssumeRole&params={}&tab=DEMO&lang=PHP
             * 你需要操作的资源所在的region，STS服务目前只有杭州节点可以签发Token，签发出的Token在所有Region都可用
             */
            AlibabaCloud::accessKeyClient($this->tempAppKey, $this->tempAppSecret)->regionId('cn-hangzhou')->asDefaultClient();
            $result             = AlibabaCloud::rpc()
                ->product('Sts')
                ->scheme('https') // https | http
                ->version('2015-04-01')
                ->action('AssumeRole')
                ->method('POST')
                ->host('sts.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId'        => "cn-hangzhou",
                        'RoleArn'         => $this->roleArn,
                        'RoleSessionName' => 'app',  // 您可以使用您的客户的ID作为会话名称
                        'DurationSeconds' => 3600,
                        'Policy'          => $policy,
                    ],
                ])
                ->request();
            $respObj            = $result->toArray();
            $resp               = $respObj['Credentials'];
            $resp['directory']  = $dir;
            $resp['prefix_url'] = $this->url;
            $resp['bucket']     = $bucket;
            $resp['endpoint']   = $this->endpoint;
            foreach ($resp as $k => $v) {
                $sk = Str::snake($k);
                if ($sk !== $k) {
                    $resp[$sk] = $v;
                    unset($resp[$k]);
                }
            }
            $this->tempKey = $resp;

            return true;
        } catch (ClientException | ServerException $e) {
            return $this->setError($e->getErrorMessage());
        }
    }
}