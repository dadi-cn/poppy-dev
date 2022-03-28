<?php

namespace Poppy\System\Tests\Action;

use Poppy\System\Action\Verification;
use Poppy\System\Tests\Base\SystemTestCase;

class VerificationTest extends SystemTestCase
{

    protected $verification;

    public function setUp(): void
    {
        parent::setUp();

        $this->verification = new Verification();
    }

    public function testCaptcha()
    {
        $Verification = new Verification();
        $mobile       = $this->faker()->phoneNumber;
        if ($Verification->genCaptcha($mobile)) {
            $captcha = $Verification->getCaptcha();
            $this->assertTrue($Verification->checkCaptcha($mobile, $captcha));
        }
        else {
            $this->fail($Verification->getError());
        }

        $mobile = $this->faker()->phoneNumber;
        $Verification->genCaptcha($mobile, 5, 4);
        $captcha = $Verification->getCaptcha();
        $this->assertEquals(4, strlen($captcha));


        $mobile = $this->faker()->phoneNumber;
        $Verification->genCaptcha($mobile, '5', '4');
        $captcha = $Verification->getCaptcha();
        $this->assertEquals(4, strlen($captcha));
    }

    /**
     * 验证一次验证码
     */
    public function testOnceCode()
    {
        $hidden   = 'once-code';
        $onceCode = $this->verification->genOnceVerifyCode(5, $hidden);
        $this->verification->verifyOnceCode($onceCode, false);
        $this->assertEquals($hidden, $this->verification->getHidden());

        // 支持数组隐藏
        $hidden   = ['a', 'b'];
        $onceCode = $this->verification->genOnceVerifyCode(5, $hidden);
        $this->verification->verifyOnceCode($onceCode, false);
        $this->assertEquals($hidden, $this->verification->getHidden());
    }
}
