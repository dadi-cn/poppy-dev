<?php namespace Poppy\Framework\Tests\Poppy;

use Poppy\Framework\Application\TestCase;
use Poppy\Framework\Helper\ArrayHelper;

class PoppyTest extends TestCase
{

	public function testGenKey(): void
	{
		$arr    = [
			'location' => 'http://www.baidu.com',
			'status'   => 'error',
		];
		$genKey = ArrayHelper::genKey($arr);

		// 组合数组
		$this->assertEquals('location|http://www.baidu.com;status|error', $genKey);

		// 组合空
		$this->assertEquals('', ArrayHelper::genKey([]));
	}


	public function testPath(): void
	{
		$this->assertEquals(base_path('framework'), app('path.framework'));
		$this->assertEquals(base_path('modules'), app('path.module'));
	}
}