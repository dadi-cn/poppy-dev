<?php namespace Poppy\Framework\Tests\Helper;

use Poppy\Framework\Application\TestCase;
use Poppy\Framework\Helper\ArrayHelper;

/**
 * ArrayHelperTest
 */
class ArrayHelperTest extends TestCase
{
	/**
	 * testCombine
	 */
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

	public function testToKvStr()
	{
		$array1 = ['a' => 'b'];

		$this->assertEquals('a=b', ArrayHelper::toKvStr($array1));

		$array2 = [
			'a' => '1',
			'b' => '2',
		];
		$this->assertEquals('a=1,b=2', ArrayHelper::toKvStr($array2, ','));

		$array3 = [
			'a' => [
				'd', 'e',
			],
			'b' => '2',
		];
		$this->assertEquals('a=["d","e"],b=2', ArrayHelper::toKvStr($array3, ','));
	}

	public function testNext()
	{
		$array = [
			'a', 'b', 'd', 'f',
		];
		$this->assertEquals('d', ArrayHelper::next($array, 'b'));
	}
}