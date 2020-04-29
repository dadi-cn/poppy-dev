<?php namespace Poppy\Framework\Tests\Helper;

use Poppy\Framework\Application\TestCase;
use Poppy\Framework\Helper\UtilHelper;

class UtilHelperTest extends TestCase
{
	/**
	 * 验证格式化格式
	 */
	public function testFormatBytes(): void
	{
		$bytes  = 3378170;
		$format = UtilHelper::formatBytes($bytes, 2);
		$this->assertEquals('3.22 MB', $format);
	}

	/**
	 * 验证身份证号
	 */
	public function testIsChid()
	{
		$format = UtilHelper::isChId('110101190001011009');
		$this->assertEquals(true, $format);
	}
}