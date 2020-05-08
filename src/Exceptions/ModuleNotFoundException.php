<?php namespace Poppy\Framework\Exceptions;

use Exception;
use Illuminate\Support\Str;

/**
 * ModuleNotFoundException
 */
class ModuleNotFoundException extends Exception
{
	/**
	 * ModuleNotFoundException constructor.
	 * @param string $slug slug
	 */
	public function __construct($slug)
	{
		$errMsg = '';
		if (!Str::contains($slug, '.')) {
			$errMsg = 'Module after version 2.x must format as `module.' . $slug . '`';
		}
		parent::__construct('Module with slug name [' . $slug . '] not found. ' . $errMsg);
	}
}