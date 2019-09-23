<?php namespace Poppy\Framework\Exceptions;

use Exception;
use Poppy\Framework\Classes\Resp;
use Throwable;

/**
 * BaseException
 */
abstract class BaseException extends Exception
{
	/**
	 * BaseException constructor.
	 * @param string         $message  message
	 * @param int            $code     code
	 * @param Throwable|null $previous previous
	 */
	public function __construct($message = '', $code = 0, Throwable $previous = null)
	{
		if ($message instanceof Resp) {
			parent::__construct($message->getMessage(), $message->getCode(), $previous);
		}
		else {
			parent::__construct($message, $code, $previous);
		}
	}
}