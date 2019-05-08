<?php namespace Poppy\Framework\Tests\Helper;

use Poppy\Framework\Application\TestCase;
use Poppy\Framework\Helper\EnvHelper;

class EnvHelperTest extends TestCase
{
	public function testIp()
	{
		$this->assertEquals('unknown', EnvHelper::ip());
	}
}