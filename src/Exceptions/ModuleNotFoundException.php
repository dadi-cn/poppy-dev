<?php namespace Poppy\Framework\Exceptions;

use Exception;

/**
 * ModuleNotFoundException
 */
class ModuleNotFoundException extends Exception
{
	/**
	 * ModuleNotFoundException constructor.
	 * @param string $slug slug
	 */
	public function __construct($slug) {
		parent::__construct('Module with slug name [' . $slug . '] not found');
	}
}