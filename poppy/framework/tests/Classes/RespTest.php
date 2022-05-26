<?php

namespace Poppy\Framework\Tests\Classes;

use Illuminate\Http\JsonResponse;
use Poppy\Framework\Application\TestCase;
use Poppy\Framework\Classes\Resp;

class RespTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        py_container()->setExecutionContext('api');
    }

    public function testStr(): void
    {
        // 原样返回
        $value = 'string';
        /** @var JsonResponse $value */
        $resp = Resp::success('string', $value);
        $data = $resp->getData(true);
        $this->assertEquals($value, $data['data']);

        // 解析 ky string
        $value = 'my|name';
        /** @var JsonResponse $value */
        $resp = Resp::success('string', $value);
        $data = $resp->getData(true);
        $this->assertEquals('name', $data['data']['my']);
    }

    public function testArray(): void
    {
        // 必须存在空数组
        $value = [];
        /** @var JsonResponse $value */
        $resp = Resp::success('array', $value);
        $data = $resp->getData(true);
        $this->assertEquals($value, $data['data']);

        // 必须原样返回数组
        $value = ['my'];
        /** @var JsonResponse $value */
        $resp = Resp::success('array', $value);
        $data = $resp->getData(true);
        $this->assertEquals($value, $data['data']);
    }

    public function testCollect()
    {
        // collection ArrayAccess
        $value = collect();
        /** @var JsonResponse $value */
        $resp = Resp::success('array', $value);
        $data = $resp->getData(true);
        $this->assertEquals([], $data['data']);

        // ArrayAccess
        $value = collect(['my']);
        /** @var JsonResponse $value */
        $resp = Resp::success('array', $value);
        $data = $resp->getData(true);
        $this->assertEquals(['my'], $data['data']);

    }
}