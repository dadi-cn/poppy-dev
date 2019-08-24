<?php namespace Poppy\Framework\Tests\Helper;

use Carbon\Carbon;
use Poppy\Framework\Application\TestCase;

class TimeHelperTest extends TestCase
{
	public function testToString(): void
	{
		$date   = Carbon::now()->subMinutes(4000);
		$result = $date->diffForHumans(null, false, false, 3);
		$this->assertEquals('2天 18小时 40分钟前', $result);
	}
}