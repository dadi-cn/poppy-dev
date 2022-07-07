<?php

namespace Poppy\Sms\Classes;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\Sms\Classes\Contracts\SmsContract;
use Poppy\Sms\Exceptions\SmsException;
use Throwable;

class AliyunSmsProvider extends BaseSms implements SmsContract
{
    use AppTrait;

    /**
     * @inheritDoc
     */
    public function send(string $type, $mobile, array $params = [], $sign = ''): bool
    {
        if (!$this->checkSms($mobile, $type, $sign)) {
            return false;
        }

        // 支持数组/字串/多字串
        $mobile = array_reduce((array) $mobile, function ($carry, $mobile) {
            $mobile = str_replace('-', '', $mobile);
            return $carry ? $carry . ',' . $mobile : $mobile;
        }, '');

        try {
            if (!class_exists('AlibabaCloud\Dysmsapi\Dysmsapi')) {
                throw new SmsException('你需要手动安装 `alibabacloud/dysmsapi` 组件');
            }

            $this->initClient();

            /**
             * @url https://help.aliyun.com/document_detail/101414.htm
             */
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => array_merge([
                        'PhoneNumbers' => $mobile,
                        'SignName'     => $this->sign,
                        'TemplateCode' => $this->sms['code'],
                    ], $params ? [
                        'TemplateParam' => json_encode($params, JSON_UNESCAPED_UNICODE),
                    ] : []),
                ])
                ->request();

            /**
             * 返回的信息如下所示, 如果失败 Message 中是错误的信息
             * {
             *    "RequestId":"04B69136-3DF5-4418-9A8C-5A9278608259",
             *    "Message":"OK",
             *    "BizId":"340723709666881413^0",
             *    "Code":"OK"
             * }
             */
            $resp = $result->toArray();
            if ($resp['Code'] === 'OK') {
                return true;
            }
            return $this->setError('Aliyun:' . $resp['Message']);
        } catch (ClientException | ServerException $e) {
            return $this->setError($e->getErrorMessage());
        } catch (SmsException $e) {
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 初始化
     * @throws SmsException
     */
    private function initClient()
    {
        $accessKeyId     = config('poppy.sms.aliyun.access_key');
        $accessKeySecret = config('poppy.sms.aliyun.access_secret');
        try {
            if (!class_exists('AlibabaCloud\Client\AlibabaCloud')) {
                throw new SmsException('你需要手动安装 `alibabacloud/client` 组件');
            }
            AlibabaCloud::accessKeyClient($accessKeyId, $accessKeySecret)
                ->regionId('cn-hangzhou')
                ->asDefaultClient();
        } catch (Throwable $e) {
            throw new SmsException($e->getMessage());
        }
    }
}
