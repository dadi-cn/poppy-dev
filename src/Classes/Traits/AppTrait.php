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
		if (is_null($this->error)) {
			return (new Resp(Resp::INNER_ERROR, '内部错误'));
		}
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
			if (!$error->getCode()) {
				$code = Resp::ERROR;
			}
			else {
				$code = $error->getCode();
			}
			$this->error = new Resp($code, $error->getMessage());
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
		if ($this->success instanceof Resp) {
			return $this->success;
		}
		if (is_string($this->success)) {
			if (empty($this->success)) {
				$this->success = '操作成功';
			}
			$this->success = new Resp(Resp::SUCCESS, $this->success);
		}
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