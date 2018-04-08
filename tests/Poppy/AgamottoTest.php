<?php namespace Poppy\Tests\Agamotto;

use Poppy\Framework\Agamotto\Agamotto;
use Poppy\Framework\Application\TestCase;

class AgamottoTest extends TestCase
{
	public function testTranslation()
	{
		Agamotto::setLocale('en');
		$weekday = strtolower(Agamotto::now()->format('l'));
		$format  = [
			'monday'    => '星期一',
			'tuesday'   => '星期二',
			'wednesday' => '星期三',
			'thursday'  => '星期四',
			'friday'    => '星期五',
			'saturday'  => '星期六',
			'sunday'    => '星期日',
		];
		Agamotto::setLocale('zh');
		$weekdayZh    = $format[$weekday];
		$weekdayTrans = Agamotto::now()->format('l');
		$this->assertTrue($weekdayZh === $weekdayTrans);
	}
}