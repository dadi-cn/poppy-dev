<?php namespace Poppy\Framework\Classes;

use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\MessageBag;
use Poppy\Framework\Helper\StrHelper;

class Resp
{
	const SUCCESS       = 0;
	const ERROR         = 1;
	const TOKEN_MISS    = 2;
	const TOKEN_TIMEOUT = 3;
	const TOKEN_ERROR   = 4;
	const PARAM_ERROR   = 5;
	const SIGN_ERROR    = 6;
	const NO_AUTH       = 7;
	const INNER_ERROR   = 99;

	const WEB_SUCCESS = 'success';
	const WEB_ERROR   = 'error';

	private $code;

	private $message = '操作出错了';

	public function __construct($code, $message = '')
	{
		// init
		if (!$code) {
			$code = self::SUCCESS;
		}

		$this->code = (int) $code;

		if (\is_string($message) && !empty($message)) {
			$this->message = $message;
		}

		if ($message instanceof MessageBag) {
			$formatMessage = [];
			foreach ($message->all(':message') as $msg) {
				$formatMessage [] = $msg;
			}
			$this->message = $formatMessage;
		}

		if (!$message) {
			switch ($code) {
				case self::SUCCESS:
					$message = trans('poppy::resp.success');
					break;
				case self::ERROR:
					$message = trans('poppy::resp.error');
					break;
				case self::TOKEN_MISS:
					$message = trans('poppy::resp.token_miss');
					break;
				case self::TOKEN_TIMEOUT:
					$message = trans('poppy::resp.token_timeout');
					break;
				case self::TOKEN_ERROR:
					$message = trans('poppy::resp.token_error');
					break;
				case self::PARAM_ERROR:
					$message = trans('poppy::resp.param_error');
					break;
				case self::SIGN_ERROR:
					$message = trans('poppy::resp.sign_error');
					break;
				case self::NO_AUTH:
					$message = trans('poppy::resp.no_auth');
					break;
				case self::INNER_ERROR:
				default:
					$message = trans('poppy::resp.inner_error');
					break;
			}
			$this->message = $message;
		}
	}

	/**
	 * 返回错误代码
	 * @return int
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * 返回错误信息
	 * @return null|string
	 */
	public function getMessage()
	{
		$env = !is_production() ? '[开发]' : '';

		return $env . (\is_string($this->message) ? $this->message : implode(',', $this->message));
	}

	/**
	 * 错误输出
	 * @param $type     int  错误码
	 * @param $msg      string|array|MessageBag  类型
	 * @param $append   string
	 *                  json: 强制以 json 数据返回
	 *                  forget : 不将错误信息返回到session 中
	 *                  location : 重定向
	 *                  reload : 刷新页面
	 *                  time   : 刷新或者重定向的时间(毫秒), 如果不填写, 默认为立即刷新或者重定向
	 *                  reload_opener : 刷新母窗口
	 * @param $input    array 表单提交的数据, 是否连带返回
	 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
	 */
	public static function web($type, $msg, $append = null, $input = null)
	{
		if (!($msg instanceof self)) {
			$resp = new self($type, $msg);
		}
		else {
			$resp = $msg;
		}

		$isJson   = false;
		$isForget = false;

		$arrAppend = StrHelper::parseKey($append);

		$arrAppend = self::append($arrAppend);

		// is json
		if (isset($arrAppend['json']) ||
			\Request::ajax() ||
			(strtolower(substr(\Input::header('Authorization'), 0, 6)) === 'bearer') ||
			Container::getInstance()->isRunningIn('api')
		) {
			$isJson = true;
			unset($arrAppend['json']);
		}

		// is forget
		if (isset($arrAppend['forget'])) {
			$isForget = true;
			unset($arrAppend['forget']);
		}

		$location = $arrAppend['location'] ?? '';
		$time     = $arrAppend['time'] ?? 0;

		if (!$isForget || \Request::ajax()) {
			\Session::flash('end.message', $resp->getMessage());
			\Session::flash('end.level', $resp->getCode());
		}

		if ($isJson) {
			return self::webSplash($resp, is_null($append) ? $append : $arrAppend, $input);
		}

		if (isset($arrAppend['reload'])) {
			$location = \Session::previousUrl();
		}

		return self::webView($time, $location, $input);
	}

	public static function data($type, $msg)
	{
		if (!($msg instanceof self)) {
			$resp = new self($type, $msg);
		}
		else {
			$resp = $msg;
		}

		return $resp->toArray();
	}

	public function __toString()
	{
		if (\is_array($this->message)) {
			return implode("\n", $this->message);
		}

		return $this->message;
	}

	public function toArray(): array
	{
		return [
			'status'  => $this->getCode(),
			'message' => $this->getMessage(),
		];
	}

	/**
	 * 返回成功输入
	 * @param string|array|MessageBag $msg    提示消息
	 * @param string                  $append 追加的信息
	 * @param string                  $input  保留输入的数据
	 * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
	 */
	public static function success($msg, $append = null, $input = null)
	{
		return self::web(self::SUCCESS, $msg, $append, $input);
	}

	/**
	 * 返回错误数组
	 * @param string|array|MessageBag $msg    提示消息
	 * @param string                  $append 追加的信息
	 * @param string                  $input  保留输入的数据
	 * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
	 */
	public static function error($msg, $append = null, $input = null)
	{
		return self::web(self::ERROR, $msg, $append, $input);
	}

	/**
	 * 返回自定义信息
	 * @param        $code
	 * @param string $message
	 * @return array
	 */
	public static function custom($code, $message = '')
	{
		return (new self($code, $message))->toArray();
	}

	/**
	 * 显示界面
	 * @param $time
	 * @param $location
	 * @param $input
	 * @return \Illuminate\Http\RedirectResponse|Resp
	 */
	private static function webView($time, $location, $input)
	{
		if ($time || $location === 'back' || $location === 'message' || !$location) {
			$re         = $location ?: 'back';
			$messageTpl = config('poppy.message_template');
			$view       = '';
			if ($messageTpl) {
				foreach ($messageTpl as $context => $tplView) {
					if (Container::getInstance()->isRunningIn($context)) {
						$view = $tplView;
					}
				}
			}

			if (!$view) {
				if (Container::getInstance()->runningInBackend()) {
					$view = 'system::backend.tpl.inc_message';
				}
				else {
					$view = 'poppy::template.message';
				}
			}

			return response()->view($view, [
				'location' => $re,
				'input'    => $input,
				'time'     => $time ?? 0,
			]);
		}

		$re = ($location && $location !== 'back') ? \Redirect::to($location) : \Redirect::back();

		return $input ? $re->withInput($input) : $re;
	}

	/**
	 * 不支持 location
	 * splash 不支持 location | back (Mark Zhao)
	 * @param Resp         $resp
	 * @param string|array $append
	 * @param array        $input
	 * @return \Illuminate\Http\JsonResponse
	 */
	private static function webSplash($resp, $append = '', $input = [])
	{
		$return = [
			'status'  => $resp->getCode(),
			'message' => $resp->getMessage(),
		];

		$data = null;
		if (!\is_null($append)) {
			if ($append instanceof Arrayable) {
				$data = $append->toArray();
			}
			elseif (\is_string($append)) {
				$data = StrHelper::parseKey($append);
			}
			elseif (\is_array($append)) {
				$data = $append;
			}
			if (isset($data['location']) && $data['location'] === 'back') {
				unset($data['location']);
			}
		}
		if (!is_null($data)) {
			$return['data'] = (array) $data;
		}

		if (\is_array($input) && $input) {
			\Session::flashInput($input);
		}

		return \Response::json($return, 200, [], JSON_UNESCAPED_UNICODE);
	}

	/**
	 * 数据分析最佳
	 * @param array $append
	 * @return array
	 */
	private static function append($append): array
	{
		if (input('_update')) {
			$append['_update']  = input('_update');
			$append['_content'] = $append['_content'] ?? '';
		}

		return $append;
	}
}