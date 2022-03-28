<?php

namespace Poppy\System\Tests\Models;

use Poppy\System\Models\PamAccount;
use Poppy\System\Tests\Base\SystemTestCase;
use Tymon\JWTAuth\JWTGuard;

class PamAccountTest extends SystemTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->initPam();
    }

    public function testPermissions()
    {
        $permissions = PamAccount::permissions($this->pam);
        $this->assertNotNull($permissions, 'User has no permission');
        $names = $permissions->pluck('name');
        $this->assertNotNull($names, 'User has no permission');
    }

    public function testJwtToken()
    {
        /** @var JWTGuard $Jwt */
        $Jwt   = auth('jwt_web');
        $token = $Jwt->tokenById($this->pam->id);

        if ($Jwt->setToken($token)->authenticate()) {
            $this->assertTrue(true);
        }
        else {
            $this->fail('use `jwt:secret` generate token');
        }
    }

    public function testType()
    {
        $type = PamAccount::passportType('a-zhou9999@qq.com');
        $this->assertEquals('email', $type);
    }
}