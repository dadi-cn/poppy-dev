<?php

namespace Poppy\System\Tests\Action;

use Poppy\System\Action\Pam;
use Poppy\System\Action\Verification;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamRole;
use Poppy\System\Tests\Base\SystemTestCase;

class PamTest extends SystemTestCase
{

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
            $this->fail($Verification->getError());
        }
        else {
            $platform = collect(array_keys(PamAccount::kvPlatform()))->random(1)[0];
            $Pam      = new Pam();
            if ($Pam->captchaLogin($mobile, $Verification->getCaptcha(), $platform)) {
                $this->assertTrue(true);
            }
            else {
                $this->fail($Pam->getError());
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
            $this->fail($Pam->getError());
        }
    }

    public function testRegisterWithUsername()
    {
        $passport = $this->faker()->lexify('test_????????');
        $password = $this->faker()->lexify('????????');
        $Pam      = new Pam();
        if ($Pam->register($passport, $password)) {
            $this->assertTrue(true);
        }
        else {
            $this->fail($Pam->getError());
        }
    }

    public function testRegisterDevelop()
    {
        $passport = $this->faker()->lexify('test_????????');
        $Pam      = new Pam();
        if ($Pam->register($passport, '', PamRole::DEV_USER)) {
            $this->assertTrue(true);
        }
        else {
            $this->fail($Pam->getError());
        }
    }


    public function testRebind()
    {
        $this->initPam();
        $mobile = $this->faker()->phoneNumber;
        $Pam    = new Pam();
        if ($Pam->rebind($this->pam, $mobile)) {
            $this->assertTrue(true);
        }
        else {
            $this->fail($Pam->getError());
        }
    }

    /**
     * 设置密码
     */
    public function testSetPassword(): void
    {
        $this->initPam();
        $Pam      = new Pam();
        $password = $this->faker()->bothify('?#?#?#');
        if ($Pam->setPassword($this->pam, $password)) {
            $this->assertTrue(true);
            if (!$Pam->loginCheck($this->pam->mobile, $password)) {
                $this->fail($Pam->getError());
            }
            else {
                $this->assertTrue(true);
            }
        }
        else {
            $this->fail($Pam->getError());
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
            $this->assertTrue(isset($pam->{$variable}), 'Error Key @ Pam : ' . $variable);
        }
    }
}