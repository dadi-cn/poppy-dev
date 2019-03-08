<?php namespace Poppy\Framework\Classes\Traits;

use Illuminate\Support\MessageBag;
use Poppy\Framework\Classes\Resp;

trait AppTrait
{
	/**
	 * @var Resp
	 */
	protected $error;

	/**
	 * @var Resp
	 */
	protected $success;

	/**
	 * 设置错误
	 * @param string|MessageBag $error
	 * @return bool
	 */
	public function setError($error)
	{
		if ($error instanceof Resp) {
			$this->error = $error;
		}
		else {
			$this->error = new Resp(Resp::ERROR, $error);
		}

		return false;
	}

	/**
	 * 获取错误
	 * @return Resp
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @param Resp|string $success 设置的成功信息
	 */
	public function setSuccess($success)
	{
		if ($success instanceof Resp) {
			$this->success = $success;
		}
		else {
			$this->success = new Resp(Resp::ERROR, $success);
		}
	}

	/**
	 * Get success messages;
	 * @return Resp
	 */
	public function getSuccess()
	{
		return $this->success;
	}
}