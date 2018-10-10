<?php namespace Poppy\Framework\Support\Abstracts;

use Illuminate\Support\Collection;

/**
 * Class Repository.
 */
abstract class Repository extends Collection
{
	/**
	 * Initialize.
	 * @param Collection $collection
	 */
	abstract public function initialize(Collection $collection);
}