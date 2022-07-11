<?php

namespace Poppy\Framework\Tests\Validation;

use Poppy\Framework\Application\TestCase;
use Poppy\Framework\Validation\Rule;
use Validator;

class ValidationTest extends TestCase
{

    public function testMobile(): void
    {
        $mobile    = '17787876656';
        $validator = Validator::make([
            'mobile' => $mobile,
        ], [
            'mobile' => Rule::mobile(),
        ], [], [
            'mobile' => '手机号',
        ]);
        if ($validator->fails()) {
            $this->fail();
        }
        else {
            $this->assertTrue(true);
        }
    }


    public function testIn(): void
    {
        $v         = 'a';
        $validator = Validator::make([
            'v' => $v,
        ], [
            'v' => Rule::in(['a', 'b']),
        ]);
        if ($validator->fails()) {
            $this->fail();
        }
        else {
            $this->assertTrue(true);
        }
    }

    public function testPwd(): void
    {
        $password  = '123';
        $validator = Validator::make([
            'password' => $password,
        ], [
            'password' => Rule::simplePwd(),
        ], [], [
            'password' => '密码',
        ]);
        if ($validator->fails()) {
            $this->fail();
        }
        else {
            $this->assertTrue(true);
        }
    }


    public function testJson(): void
    {
        $json      = '{}';
        $validator = Validator::make([
            'json' => $json,
        ], [
            'json' => Rule::json(),
        ], [], [
            'json' => 'Json',
        ]);
        if ($validator->fails()) {
            $this->fail();
        }
        else {
            $this->assertTrue(true);
        }
    }


    public function testDate(): void
    {
        $date      = '2011-12-05';
        $validator = Validator::make([
            'date' => $date,
        ], [
            'date' => Rule::date(),
        ], [], [
            'date' => 'Date',
        ]);
        if ($validator->fails()) {
            $this->fail();
        }
        else {
            $this->assertTrue(true);
        }
    }


    public function testChid(): void
    {
        $chid      = '640181200809108307';
        $validator = Validator::make([
            'chid' => $chid,
        ], [
            'chid' => Rule::chid(),
        ], [], [
            'chid' => 'Chid',
        ]);
        if ($validator->fails()) {
            $this->fail();
        }
        else {
            $this->assertTrue(true);
        }

        $chid      = '3622012.0508072';
        $validator = Validator::make([
            'chid' => $chid,
        ], [
            'chid' => Rule::chid(),
        ], [], [
            'chid' => 'Chid',
        ]);
        if ($validator->fails()) {
            $this->assertTrue(true);
        }
        else {
            $this->fail();
        }
    }

    public function testUsername()
    {
        // false
        $str       = '我是中国人---xxx';
        $validator = Validator::make([
            'len' => $str,
        ], [
            'len' => [
                Rule::username(),
            ],
        ]);
        if ($validator->fails()) {
            $this->assertTrue(true, $validator->messages()->toJson(JSON_UNESCAPED_UNICODE));
        }
        else {
            $this->fail('该验证不应通过');
        }
    }


    public function testLength(): void
    {
        $str       = '我是中国人';
        $validator = Validator::make([
            'len' => $str,
        ], [
            'len' => [
                Rule::max(6),
            ],
        ]);
        if ($validator->fails()) {
            $this->fail($validator->messages()->toJson(JSON_UNESCAPED_UNICODE));
        }
        else {
            $this->assertTrue(true);
        }
    }

    public function testDigit(): void
    {
        $str       = '1234';
        $validator = Validator::make([
            'v' => $str,
        ], [
            'v' => [
                Rule::digits(4),
            ],
        ]);
        if ($validator->fails()) {
            $this->fail($validator->messages()->toJson(JSON_UNESCAPED_UNICODE));
        }
        else {
            $this->assertTrue(true);
        }
    }
}