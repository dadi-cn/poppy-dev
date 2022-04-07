<?php

namespace Poppy\Extension\Alipay\Tests\Aop;

use Poppy\Extension\Alipay\Aop\Request\AlipaySystemOauthTokenRequest;
use Poppy\Extension\Alipay\Tests\AlipayBaseTest;
use Throwable;

class SystemTest extends AlipayBaseTest
{

    /**
     * 使用证书方式进行转账
     */
    public function testOauthToken(): void
    {
        $aop     = $this->client();
        $request = new AlipaySystemOauthTokenRequest();
        $request->setGrantType('authorization_code');
        $request->setCode('democode');

        try {
            $result = $aop->execute($request);
            $resp = data_get($result, 'error_response');
            $this->assertEquals('40002', data_get($resp, 'code'));
        } catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }
}