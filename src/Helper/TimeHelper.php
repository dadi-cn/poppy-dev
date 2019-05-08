<?php namespace Poppy\Framework\Helper;

use Carbon\Carbon;
use DateTime as PhpDateTime;
use Exception;

class TimeHelper
{
	/**
	 * 标准的UNIX时间戳
	 * 获得当前格林威治时间的时间戳
	 * @return int
	 */
	public static function gmTime(): int
	{
		return time() - date('Z');
	}

	/**
	 * @param        $date
	 * @param string $sep
	 * @return bool 检测是否是标准时间
	 */
	public static function isDate($date, $sep = '-'): bool
	{
		// 时间为空
		if (empty($date)) {
			return false;
		}
		// 长度大于 10
		if (strlen($date) > 10) {
			return false;
		}
		list($year, $month, $day) = explode($sep, $date);

		return checkdate($month, $day, $year);
	}

	/**
	 * 格式化时间
	 * @param int    $time
	 * @param string $format
	 * @return bool|string
	 */
	public static function datetime($time = 0, $format = '3-3')
	{
		if (!empty($time)) {
			if (!is_numeric($time)) {
				// strotime强制将代入进来的时间格式都转成Unix时间戳
				$time = strtotime($time);
			}
		}
		else {
			$time = EnvHelper::time();
		}
		switch ($format) {
			case '3-2':
				$df = 'Y-m-d H:i';
				break;
			case '2-2':
				$df = 'm-d H:i';
				break;
			case '2-3':
				$df = 'm-d H:i:s';
				break;
			case '2-4':
				$df = 'Y-m-d';
				break;
			case '3-3':
			default:
				$df = 'Y-m-d H:i:s';
				break;
		}

		return date($df, $time);
	}

	/**
	 * 自定义函数：time2string($second) 输入秒数换算成多少天/多少小时/多少分/多少秒的字符串
	 * @param $second
	 * @return string
	 */
	public static function time2string($second)
	{
		$day    = floor($second / (3600 * 24));
		$second = $second % (3600 * 24);//除去整天之后剩余的时间
		$hour   = floor($second / 3600);
		$second = $second % 3600;//除去整小时之后剩余的时间
		$minute = floor($second / 60);
		$second = $second % 60;//除去整分钟之后剩余的时间
		//返回字符串
		//return $day.'天'.$hour.'小时'.$minute.'分'.$second.'秒';
		return $day . '天' . $hour . '小时';
	}

	/**
	 * 转换字符串形式的时间表达式为GMT时间戳
	 * @param $str
	 * @return bool|int|string
	 */
	public static function gmStr2Time($str)
	{
		$time = strtotime($str);

		if ($time > 0) {
			$time -= date('Z');
		}

		return $time;
	}

	/**
	 * 获得服务器的时区
	 * @return float|string
	 */
	public static function serverTimezone()
	{
		if (function_exists('date_default_timezone_get')) {
			return date_default_timezone_get();
		}

		return date('Z') / 3600;
	}

	/**
	 * 一天的开始
	 * @param $date
	 * @return string
	 */
	public static function dayStart($date)
	{
		if (preg_match('/\d{4}-\d{2}-\d{2}/', $date)) {
			$date = Carbon::createFromFormat('Y-m-d', $date)->timestamp;
		}

		return date('Y-m-d', $date) . ' 00:00:00';
	}

	/**
	 * 一天的结束
	 * @param $date
	 * @return string
	 */
	public static function dayEnd($date)
	{
		if (preg_match('/\d{4}-\d{2}-\d{2}/', $date)) {
			$date = Carbon::createFromFormat('Y-m-d', $date)->timestamp;
		}

		return date('Y-m-d', $date) . ' 23:59:59';
	}

	/**
	 * 格式化日期
	 * @param int    $time
	 * @param string $format
	 * @return bool|string
	 */
	public static function format($time = 0, $format = 'Y-m-d H:i')
	{
		//strtotime 强制将代入进来的时间格式都转成Unix时间戳
		$timestamp = !empty($time) ? (is_numeric($time) ? $time : strtotime($time)) : EnvHelper::time();

		return date($format, $timestamp);
	}

	/**
	 * 空日期检测
	 * @param $date
	 * @return bool
	 */
	public static function isEmpty($date)
	{
		if (empty($date) or $date === '0000-00-00' or $date === '0000-00-00 00:00:00') {
			return true;
		}

		return false;
	}

	/**
	 * @param $datetime
	 * @return int
	 */
	public static function datetimeToTimestamp($datetime)
	{
		return Carbon::createFromFormat('Y-m-d H:i:s', $datetime)->timestamp;
	}

	/**
	 * 时间戳转换成 datetime 类型
	 * @param $timestamp
	 * @return bool|string
	 */
	public static function timestampToDatetime($timestamp)
	{
		return self::format($timestamp, 'Y-m-d H:i:s');
	}

	/**
	 * 获取今日起始时间
	 * @param bool|false $unix
	 * @return int|string
	 */
	public static function todayStart($unix = false)
	{
		$Carbon = Carbon::now()->hour(0)->minute(0)->second(0);
		if ($unix) {
			return $Carbon->timestamp;
		}

		return $Carbon->toDateTimeString();
	}

	/**
	 * 获取今日起始时间
	 * @param bool|false $unix
	 * @return int|string
	 */
	public static function todayEnd($unix = false)
	{
		$Carbon = Carbon::now()->hour(23)->minute(59)->second(59);
		if ($unix) {
			return $Carbon->timestamp;
		}

		return $Carbon->toDateTimeString();
	}

	/*
	 * 精确时间间隔函数
	 * $time 发布时间 如 1356973323
	 * $str 输出格式 如 Y-m-d H:i:s
	 * 半年的秒数为15552000，1年为31104000，此处用半年的时间
	 */
	public static function tranTime($time)
	{
		$rtime = date('m-d H:i', $time);
		$htime = date('H:i', $time);

		$time = time() - $time;

		if ($time < 60) {
			$str = '刚刚';
		}
		elseif ($time < 60 * 60) {
			$min = floor($time / 60);
			$str = $min . '分钟前';
		}
		elseif ($time < 60 * 60 * 24) {
			$h   = floor($time / (60 * 60));
			$str = $h . '小时前 ' . $htime;
		}
		elseif ($time < 60 * 60 * 24 * 3) {
			$d = floor($time / (60 * 60 * 24));
			if ($d == 1) {
				$str = '昨天 ' . $rtime;
			}
			else {
				$str = '前天 ' . $rtime;
			}
		}
		else {
			$str = $rtime;
		}

		return $str;
	}

	/**
	 * Returns a human readable time difference from the value to the
	 * current time. Eg: **10 minutes ago**
	 *
	 * @return string
	 */
	public static function timeSince($datetime)
	{
		return self::makeCarbon($datetime)->diffForHumans();
	}

	/**
	 * Returns 24-hour time and the day using the grammatical tense
	 * of the current time. Eg: Today at 12:49, Yesterday at 4:00
	 * or 18 Sep 2015 at 14:33.
	 *
	 * @return string
	 */
	public static function timeTense($datetime)
	{
		$datetime  = self::makeCarbon($datetime);
		$yesterday = $datetime->subDays(1);
		$tomorrow  = $datetime->addDays(1);
		$time      = $datetime->format('H:i');
		$date      = $datetime->format('j M Y');

		if ($datetime->isToday()) {
			$date = 'Today';
		}
		elseif ($datetime->isYesterday()) {
			$date = 'Yesterday';
		}
		elseif ($datetime->isTomorrow()) {
			$date = 'Tomorrow';
		}

		return $date . ' at ' . $time;
	}

	/**
	 * Converts mixed inputs to a Carbon object.
	 *
	 * @param Carbon|PhpDateTime|mixed $value
	 * @param bool                     $throwException
	 * @return Carbon|null
	 */
	public static function makeCarbon($value, $throwException = true)
	{
		if ($value instanceof PhpDateTime) {
			$value = Carbon::instance($value);
		}
		if (is_numeric($value)) {
			$value = Carbon::createFromTimestamp($value);
		}
		if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value)) {
			$value = Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
		}

		try {
			return Carbon::parse($value);
		} catch (Exception $ex) {
			return null;
		}
	}

	/**
	 * Converts a PHP date format to "Moment.js" format.
	 * @param string $format
	 * @return string
	 */
	public static function momentFormat($format)
	{
		$replacements = [
			'd' => 'DD',
			'D' => 'ddd',
			'j' => 'D',
			'l' => 'dddd',
			'N' => 'E',
			'S' => 'o',
			'w' => 'e',
			'z' => 'DDD',
			'W' => 'W',
			'F' => 'MMMM',
			'm' => 'MM',
			'M' => 'MMM',
			'n' => 'M',
			't' => '', // no equivalent
			'L' => '', // no equivalent
			'o' => 'YYYY',
			'Y' => 'YYYY',
			'y' => 'YY',
			'a' => 'a',
			'A' => 'A',
			'B' => '', // no equivalent
			'g' => 'h',
			'G' => 'H',
			'h' => 'hh',
			'H' => 'HH',
			'i' => 'mm',
			's' => 'ss',
			'u' => 'SSS',
			'e' => 'zz', // deprecated since version 1.6.0 of moment.js
			'I' => '', // no equivalent
			'O' => '', // no equivalent
			'P' => '', // no equivalent
			'T' => '', // no equivalent
			'Z' => '', // no equivalent
			'c' => '', // no equivalent
			'r' => '', // no equivalent
			'U' => 'X',
		];

		foreach ($replacements as $from => $to) {
			$replacements['\\' . $from] = '[' . $from . ']';
		}

		$momentFormat = strtr($format, $replacements);

		return $momentFormat;
	}

	/**
	 * 返回微秒数值
	 * @return string
	 */
	public static function micro()
	{
		list($micro, $second) = explode(' ', microtime());

		return sprintf("%'.03d", $micro * 1000);
	}

	/**
	 * 通过 Carbon 对象来获取格式化的时间
	 * @param Carbon|null|string $carbon
	 * @param string             $format
	 * @return string
	 */
	public static function fetchFormat($carbon, $format = 'Y-m-d H:i:s'): string
	{
		if ($carbon instanceof Carbon) {
			return $carbon->format($format);
		}

		if (is_string($carbon)) {
			return $carbon;
		}

		return '';
	}
}