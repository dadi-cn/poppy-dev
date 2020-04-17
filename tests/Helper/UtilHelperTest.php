<?php namespace Poppy\Framework\Tests\Helper;

use Poppy\Framework\Application\TestCase;
use Poppy\Framework\Helper\UtilHelper;

class UtilHelperTest extends TestCase
{
	public function testFormatBytes(): void
	{
		$bytes  = 3378170;
		$format = UtilHelper::formatBytes($bytes, 2);
		$this->assertEquals('3.22 MB', $format);
	}
}