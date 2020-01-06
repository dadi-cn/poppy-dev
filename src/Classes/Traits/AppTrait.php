<?php namespace Poppy\Framework\Classes\Traits;

use Exception;
use Illuminate\Support\MessageBag;
use Poppy\Framework\Classes\Resp;

/**
 * AppTrait
 */
trait AppTrait
{
	/**
	 * error
	 * @var Resp $error
	 */
	protected $error;

	/**
	 * success
	 * @var Resp $success
	 */
	protected $success;

	/**
	 * 获取错误
	 * @return Resp
	 */
	public function getError(): Resp
	{
		return $this->error;
	}

	/**
	 * 设置错误
	 * @param string|MessageBag $error error
	 * @return bool
	 */
	public function setError($error): bool
	{
		if ($error instanceof Resp) {
			$this->error = $error;
		}
		elseif ($error instanceof Exception) {
			$this->error = new Resp($error->getCode(), $error->getMessage());
		}
		else {
			$this->error = new Resp(Resp::ERROR, $error);
		}

		return false;
	}

	/**
	 * Get success messages;
	 * @return Resp
	 */
	public function getSuccess(): Resp
	{
		return $this->success;
	}

	/**
	 * @param Resp|string $success 设置的成功信息
	 * @return bool
	 */
	public function setSuccess($success): bool
	{
		if ($success instanceof Resp) {
			$this->success = $success;
		}
		else {
			$this->success = new Resp(Resp::SUCCESS, $success);
		}

		return true;
	}
}