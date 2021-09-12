<?php

namespace Poppy\AliyunOss\Tests\Action;

use OSS\Core\OssException;
use OSS\OssClient;
use Poppy\AliyunOss\Action\Sts;
use Poppy\System\Tests\Base\SystemTestCase;

class StsTest extends SystemTestCase
{
    /**
     * @var array config
     */
    private $config;

    public function setUp(): void
    {
        parent::setUp();
        $this->config = $this->readJson('poppy.aliyun-oss', 'tests/config/account.test.json');
    }

    /**
     * 测试授权KEY以及是否可以上传URL
     */
    public function testTempKey()
    {
        $config = $this->config;
        $Sts    = new Sts();
        $Sts->setConfig($config['temp_key'], $config['temp_secret'], $config['bucket'], $config['endpoint'], $config['arn'], $config['url']);
        if ($Sts->tempOss()) {
            $temp = $Sts->getTempKey();
            $this->outputVariables($temp);
            $this->assertIsArray($temp);
            $this->assertArrayHasKey("security_token", $temp);
            $this->assertArrayHasKey("access_key_id", $temp);
            $this->assertArrayHasKey("expiration", $temp);
            // test upload

            $accessKeyId     = $temp['access_key_id'];
            $accessKeySecret = $temp['access_key_secret'];
            $endpoint        = $config['endpoint'];

            // 测试上传文件
            try {
                $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint, false, $temp['security_token']);
                $url       = $temp['directory'] . '/demo.jpg';
                $ossClient->uploadFile($config['bucket'], $url, poppy_path('poppy.aliyun-oss', 'tests/files/demo.jpg'));
                $file = $config['url'] . '/' . $url;
                $this->outputVariables($file);
                $content = file_get_contents($file);
                $this->assertGreaterThan(0, strlen($content));
            } catch (OssException $e) {
                print $e->getMessage();
            }
        }
        else {
            $this->fail($Sts->getError());
        }
    }
}
