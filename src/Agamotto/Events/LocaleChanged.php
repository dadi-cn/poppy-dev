<?php namespace Poppy\Framework\Agamotto\Events;

use Poppy\Framework\Application\Event;

class LocaleChanged extends Event
{
	/**
	 * @var string
	 */
	public $locale;


	public function __construct($locale)
	{
		$this->locale = $locale;
	}
}