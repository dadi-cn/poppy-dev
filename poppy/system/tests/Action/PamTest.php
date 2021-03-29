<?php

namespace Poppy\System\Tests\Action;

use Poppy\System\Action\Pam;
use Poppy\System\Action\Verification;
use Poppy\System\Models\PamAccount;
use Poppy\System\Tests\Base\SystemTestCase;

class PamTest extends SystemTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->initPam();
    }

    /**
     * 验证码注册
     */
    public function testCaptchaLogin(): void
    {
        // 一个虚拟手机号
        $mobile = $this->faker()->phoneNumber;

        // 发送验证码
        $Verification = new Verification();
        if (!$Verification->genCaptcha($mobile)) {
            $this->assertTrue(false, $Verification->getError());
        }
        else {
            $platform = collect(array_keys(PamAccount::kvPlatform()))->random(1)[0];
            $Pam      = new Pam();
            if ($Pam->captchaLogin($mobile, $Verification->getCaptcha(), $platform)) {
                $this->assertTrue(true);
            }
            else {
                $this->assertTrue(false, $Pam->getError());
            }
        }
    }

    /**
     * 空密码注册
     */
    public function testRegisterWithEmptyPassword(): void
    {
        // 一个虚拟手机号
        $mobile = $this->faker()->phoneNumber;

        $Pam = new Pam();
        if ($Pam->register($mobile)) {
            $this->assertTrue(true);
        }
        else {
            $this->assertTrue(false, $Pam->getError());
        }
    }

    /**
     * 设置密码
     */
    public function testSetPassword(): void
    {
        $Pam      = new Pam();
        $password = $this->faker()->bothify('?#?#?#');
        if ($Pam->setPassword($this->pam, $password)) {
            $this->assertTrue(true);
        }
        else {
            $this->assertTrue(false, $Pam->getError());
        }
    }

    /**
     * 输出变量
     */
    public function testOutput()
    {
        $pam       = $this->pam;
        $variables = [
            'id',
            'username',
            'mobile',
            'email',
            'parent_id',
            'password',
            'password_key',
            'type',
            'is_enable',
            'disable_reason',
            'disable_start_at',
            'disable_end_at',
            'login_times',
            'login_ip',
            'reg_ip',
            'reg_platform',
            'remember_token',
            'created_at',
            'logined_at',
            'updated_at',
        ];

        foreach ($variables as $variable) {
            $this->outputVariables($pam->{$variable});
            $this->assertTrue(true);
        }
    }
}