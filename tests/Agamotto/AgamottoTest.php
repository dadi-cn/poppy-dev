<?php namespace Poppy\Framework\Tests\Agamotto;

use Poppy\Framework\Agamotto\Agamotto;
use Poppy\Framework\Application\TestCase;
use Poppy\Framework\Events\LocaleChanged;

class AgamottoTest extends TestCase
{
	public function testTranslation(): void
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
		$this->assertSame($weekdayZh, $weekdayTrans);
	}

	public function testLocaleChanged(): void
	{
		Agamotto::setLocale('en');
		$weekday   = strtolower(Agamotto::now()->format('l'));
		$format    = [
			'monday'    => '星期一',
			'tuesday'   => '星期二',
			'wednesday' => '星期三',
			'thursday'  => '星期四',
			'friday'    => '星期五',
			'saturday'  => '星期六',
			'sunday'    => '星期日',
		];
		$weekdayZh = $format[$weekday];
		event(new LocaleChanged('zh'));
		$weekdayTrans = Agamotto::now()->format('l');
		$this->assertSame($weekdayZh, $weekdayTrans);
	}
}