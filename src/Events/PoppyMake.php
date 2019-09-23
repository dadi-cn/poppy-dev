<?php namespace Poppy\Framework\Events;

use Poppy\Framework\Application\Event;

/**
 * PoppyMake
 */
class PoppyMake extends Event
{
	/**
	 * @var string
	 */
	public $slug;

	/**
	 * PoppyMake constructor.
	 * @param string $slug slug
	 */
	public function __construct($slug)
	{
		$this->slug = $slug;
	}
}