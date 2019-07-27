<?php namespace Poppy\Framework\Exceptions;

use Exception;
use Poppy\Framework\Classes\Resp;
use Throwable;

abstract class BaseException extends Exception
{
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