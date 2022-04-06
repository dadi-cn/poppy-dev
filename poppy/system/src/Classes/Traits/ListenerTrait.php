<?php

namespace Poppy\System\Classes\Traits;

use Poppy\Framework\Classes\Traits\AppTrait;

/**
 * Listener Helpers
 */
trait ListenerTrait
{
	/**
	 * Im 返回参数检测, 并且记录日志
	 * @param mixed  $event  时间
	 * @param string $class  类
	 * @param array  $result 结果
	 */
	public function listenIm($event, $class, $result)
	{
		if ($result['code'] !== 200) {
			sys_error($event, $class, 'listen im:' . data_get($result, 'desc'));
		}
		else {
			sys_info($event, $class, 'listen im:' . data_get($result, 'data.msgid'));
		}
	}

	/**
	 * Im 返回参数检测, 并且记录日志
	 * @param mixed  $event  事件
	 * @param string $class  类
	 * @param array  $result 结果
	 */
	public function listenSocket($event, $class, $result)
	{
		if (isset($result['error']) && $result['error']) {
			sys_error($event, $class, 'listen Socket : ' . $result['error'] ?? '');
		}
		else {
			sys_info($event, $class, $result['channels'] ?? []);
		}
	}

	/**
	 * 检测 action 状态并进行日志记录
	 * @param mixed    $event  事件
	 * @param string   $class  类
	 * @param bool     $result 结果
	 * @param AppTrait $item   条目
	 * @param string   $append 追加信息
	 */
	public function listenAction($event, $class, $result, $item, $append = '')
	{
		if ($result) {
			sys_info($event, $class, $append);
		}
		else {
			if (is_callable([$item, 'getError'])) {
				$error = $item->getError();
			}
			else {
				$error = 'Unknown error.';
			}
			sys_error($event, $class, 'listen action : ' . $error . $append);
		}
	}
}