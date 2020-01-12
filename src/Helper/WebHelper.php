<?php namespace Poppy\Framework\Helper;

use Session;

/**
 * 搜素排序
 */
class WebHelper
{
	const GO = '__go';

	/**
	 * 设置要走向的地方
	 * @return mixed
	 */
	public static function getGo()
	{
		return Session::get(self::GO);
	}

	/**
	 * 设置要去的操作
	 * @param string $url url
	 */
	public static function setGo($url)
	{
		Session::set(self::GO, $url);
	}

	/**
	 * 获取要走向的地址, 并且清除地址
	 * @return mixed
	 */
	public static function getGoAndClear()
	{
		$go = Session::get(self::GO);
		Session::remove(self::GO);

		return $go;
	}
}