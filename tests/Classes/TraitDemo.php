<?php namespace Poppy\Framework\Tests\Classes;

use Exception;
use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\Framework\Exceptions\ApplicationException;

class TraitDemo
{
	use AppTrait;

	public function error()
	{
		return $this->setError('This is an Error String');
	}

	public function exception()
	{
		return $this->setError((new Exception('This is an Exception String')));
	}

	public function exceptionWithCode($code)
	{
		return $this->setError((new ApplicationException('This is an Exception With Code', $code)));
	}
}