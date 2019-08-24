<?php namespace Poppy\Framework\Helper;

use Illuminate\Support\Arr;
use function is_array;

/**
 * 数组相关操作
 */
class ArrayHelper extends Arr
{
	/**
	 * 拼合数组, 支持多维拼合
	 * @param array  $array 输入的数组
	 * @param string $join  间隔字串
	 * @return string
	 */
	public static function combine(array $array, $join = ','): string
	{
		$arr = self::flatten($array);

		return implode($join, $arr);
	}

	/**
	 * 根据数组生成自定义key序列, | 作为 kv 分隔, ; 作为 kv 之间的分隔
	 * ['name'=>'mark Zhao'] 转化为 name|mark Zhao
	 * @param array $array 输入的数组
	 * @return string 返回的字串
	 */
	public static function genKey($array): string
	{
		if ($array) {
			$str = '';
			foreach ($array as $key => $value) {
				if (is_numeric($key)) {
					continue;
				}
				if (!$value) {
					$value = 0;
				}
				$str .= $key . '|' . $value . ';';
			}

			return rtrim($str, ';');
		}

		return '';
	}

	/**
	 * 返回kv结构字串
	 * @param array  $array
	 * @param string $join
	 * @return string
	 */
	public static function toKvStr($array, $join = ','): string
	{
		$return = '';

		if (is_array($array)) {
			foreach ($array as $key => $value) {
				if (is_array($value)) {
					ksort($value);
					$return .= $key . '=' . json_encode($value) . $join;
				}
				else {
					$return .= $key . '=' . $value . $join;
				}
			}
		}
		else {
			$return .= $array . $join;
		}

		return rtrim($return, $join);
	}

	/**
	 * 根据当前值, 获取下一个值
	 * @param array      $array 请求的数组
	 * @param string|int $value 匹配的值
	 * @return mixed
	 */
	public static function next($array, $value)
	{
		reset($array);
		while ($_val = current($array)) {
			if ($_val === $value) {
				break;
			}
			next($array);
		}

		return next($array);
	}
}