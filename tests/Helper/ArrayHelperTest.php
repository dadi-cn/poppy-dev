<?php namespace Poppy\Tests\Helper;

use PHPUnit\Framework\TestCase;
use Poppy\Framework\Helper\ArrayHelper;
use Poppy\Framework\Helper\UtilHelper;

class ArrayHelperTest extends TestCase
{
	public function testCombine()
	{
		$arr     = [
			1, 2, 3, [4, 5], 6, 7,
		];
		$combine = ArrayHelper::combine($arr);
		$this->assertEquals('1,2,3,4,5,6,7', $combine);
	}

	public function testGenKey()
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

	public function testIsComma()
	{
		$str = '221,22,12';
		$res = UtilHelper::isComma($str);
		dd($res);
	}

	public function testNext()
	{
		$array = [
			'a', 'b', 'd', 'f'
		];
		$this->assertEquals('d', ArrayHelper::next($array, 'b'));
	}
}