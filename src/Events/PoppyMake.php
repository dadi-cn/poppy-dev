<?php namespace Poppy\Framework\Events;

use Poppy\Framework\Application\Event;

class PoppyMake extends Event
{
	/**
	 * @var string
	 */
	public $slug;


	public function __construct($slug)
	{
		$this->slug = $slug;
	}
}