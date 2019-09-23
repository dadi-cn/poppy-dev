<?php namespace Poppy\Framework\Exceptions;

use Exception;

/**
 * PolicyException
 */
class PolicyException extends Exception
{
	/**
	 * @var int $code
	 */
	protected $code = 101;
}