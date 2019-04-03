<?php namespace Poppy\Framework\Helper;

/**
 * 环境获取
 */
class EnvHelper
{
	/**
	 * 返回 IP 信息
	 * @return string 返回IP
	 */
	public static function ip(): string
	{
		$_SERVER['HTTP_X_FORWARDED_FOR'] = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
		$_SERVER['REMOTE_ADDR']          = $_SERVER['REMOTE_ADDR'] ?? '';
		$_SERVER['HTTP_CLIENT_IP']       = $_SERVER['HTTP_CLIENT_IP'] ?? '';

		if ($_SERVER['HTTP_X_FORWARDED_FOR'] && $_SERVER['REMOTE_ADDR']) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			if (strpos($ip, ',') !== false) {
				$tmp = explode(',', $ip);
				$ip  = trim(end($tmp));
			}
			if (UtilHelper::isIp($ip)) {
				return $ip;
			}
		}
		if (UtilHelper::isIp($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		if (UtilHelper::isIp($_SERVER['REMOTE_ADDR'])) {
			return $_SERVER['REMOTE_ADDR'];
		}

		return 'unknown';
	}

	/**
	 * 当前执行的脚本文件的名称
	 * @return string 当前文件的名称
	 */
	public static function self(): string
	{
		return $_SERVER['PHP_SELF']
			?? $_SERVER['SCRIPT_NAME']
			?? $_SERVER['ORIG_PATH_INFO'];
	}

	/**
	 * 来源地址
	 * @return string 来源地址
	 */
	public static function referer(): string
	{
		return $_SERVER['HTTP_REFERER'] ?? '';
	}

	/**
	 * 返回服务器的名称
	 * @return string 返回服务器名称
	 */
	public static function domain(): string
	{
		return $_SERVER['SERVER_NAME'] ?? '';
	}

	/**
	 * @return string 协议名称
	 */
	public static function scheme(): string
	{
		if (!isset($_SERVER['SERVER_PORT'])) {
			return 'http://';
		}

		return (string) $_SERVER['SERVER_PORT'] === '443' ? 'https://' : 'http://';
	}

	/**
	 * @return string 返回端口号
	 */
	public static function port(): string
	{
		if (!isset($_SERVER['SERVER_PORT'])) {
			return '';
		}

		return (int) $_SERVER['SERVER_PORT'] === '80' ? '' : ':' . $_SERVER['SERVER_PORT'];
	}

	/**
	 * @return string 完整的地址
	 */
	public static function uri(): string
	{
		if (isset($_SERVER['REQUEST_URI'])) {
			$uri = $_SERVER['REQUEST_URI'];
		}
		else {
			$uri = $_SERVER['PHP_SELF'];
			if (isset($_SERVER['argv'])) {
				if (isset($_SERVER['argv'][0])) {
					$uri .= '?' . $_SERVER['argv'][0];
				}
			}
			else {
				$uri .= '?' . $_SERVER['QUERY_STRING'];
			}
		}
		$uri = StrHelper::htmlSpecialChars($uri);

		return self::scheme() . self::host() . (strpos(self::host(), ':') === false ? self::port() : '') . $uri;
	}

	/**
	 * 获取主机
	 * @return string
	 */
	public static function host(): string
	{
		return $_SERVER['HTTP_HOST'] ?? '';
	}

	/**
	 * @return string   没有查询的完整的URL地址, 基于当前页面
	 */
	public static function nqUrl(): string
	{
		return self::scheme() . self::host() . (strpos(self::host(), ':') === false ? self::port() : '') . self::self();
	}

	/**
	 * 请求的unix 时间戳
	 * @return int
	 */
	public static function time(): int
	{
		return (int) $_SERVER['REQUEST_TIME'];
	}

	/**
	 * 浏览器头部
	 * @return string
	 */
	public static function agent(): string
	{
		return $_SERVER['HTTP_USER_AGENT'] ?? '';
	}

	/**
	 * 是否是代理
	 * @return bool
	 */
	public static function isProxy(): bool
	{
		return
			(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ||
			isset($_SERVER['HTTP_VIA']) ||
			isset($_SERVER['HTTP_PROXY_CONNECTION']) ||
			isset($_SERVER['HTTP_USER_AGENT_VIA']) ||
			isset($_SERVER['HTTP_CACHE_INFO']);
	}

	/**
	 * 是否win 服务器
	 * @return bool
	 */
	public static function isWindows(): bool
	{
		if ('DARWIN' === strtoupper(PHP_OS)) {
			return false;
		}

		return stripos(PHP_OS, 'WIN') !== false;
	}

	/**
	 * 获取客户端OS
	 * @return array|string
	 */
	public static function os()
	{
		$agent = self::agent();
		if (false !== stripos($agent, 'win')) {
			$os = 'windows';
		}
		elseif (false !== stripos($agent, 'linux')) {
			$os = 'linux';
		}
		elseif (false !== stripos($agent, 'unix')) {
			$os = 'unix';
		}
		elseif (false !== stripos($agent, 'mac')) {
			$os = 'Macintosh';
		}
		else {
			$os = 'other';
		}

		return $os;
	}
}