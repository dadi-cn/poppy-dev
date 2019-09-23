<?php namespace Poppy\Framework\Events;

use Poppy\Framework\Application\Event;

/**
 * LocaleChanged
 */
class LocaleChanged extends Event
{
	/**
	 * @var string $locale
	 */
	public $locale;

	/**
	 * LocaleChanged constructor.
	 * @param string $locale locale
	 */
	public function __construct($locale)
	{
		$this->locale = $locale;
	}
}