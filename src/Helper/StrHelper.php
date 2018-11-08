<?php namespace Poppy\Framework\Helper;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

/**
 * 字串处理
 */
class StrHelper extends Str
{
	/**
	 * @param string $string
	 * @param string $split
	 * @return string 获取文件名后缀名
	 */
	public static function suffix($string, $split = '.')
	{
		return strtolower(trim(substr(strrchr($string, $split), 1)));
	}

	/**
	 * @param        $string
	 * @param string $split
	 * @return string 获取文件名前缀
	 */
	public static function prefix($string, $split = '.')
	{
		return strtolower(trim(substr($string, 0, strpos($string, $split))));
	}

	/**
	 * 检测是否含有空格符
	 * @param $value
	 * @return int
	 */
	public static function hasSpace($value)
	{
		return preg_match('/\s+/', $value);
	}

	/**
	 * 取消转义
	 * @param $input
	 * @return array|string
	 */
	public static function stripSlashes($input)
	{
		return is_array($input) ? array_map([__CLASS__, __FUNCTION__], $input) : stripslashes($input);
	}

	/**
	 * 转义操作
	 * @param $input
	 * @return array|string
	 */
	public static function addSlashes($input)
	{
		return is_array($input) ? array_map([__CLASS__, __FUNCTION__], $input) : addslashes($input);
	}

	/**
	 * 转义特殊字符
	 * @param      $input
	 * @param bool $preserveAmpersand
	 * @return array|mixed|string
	 */
	public static function htmlSpecialChars($input, $preserveAmpersand = true)
	{
		if (is_string($input)) {
			if ($preserveAmpersand) {
				return str_replace('&amp;', '&', htmlspecialchars($input, ENT_QUOTES));
			}

			return htmlspecialchars($input, ENT_QUOTES);
		}
		if (is_array($input)) {
			foreach ($input as $key => $val) {
				$input[$key] = self::htmlSpecialChars($val, $preserveAmpersand);
			}

			return $input;
		}

		return $input;
	}

	/**
	 * 能做到代码不危害大众, 但是还不能把代码安全展示出来
	 * @param $input
	 * @return array|mixed
	 */
	public static function safe($input)
	{
		if (is_array($input)) {
			return array_map([__CLASS__, __FUNCTION__], $input);
		}

		if (strlen($input) < 20) return $input;
		$match   = [
			'/&#([a-z0-9]+)([;]*)/i',
			"/(j[\s\r\n\t]*a[\s\r\n\t]*v[\s\r\n\t]*a[\s\r\n\t]*s[\s\r\n\t]*c[\s\r\n\t]*r[\s\r\n\t]*i[\s\r\n\t]*p[\s\r\n\t]*t|jscript|js|vbscript|vbs|about|expression|script|frame|link|import)/i",
			'/on(mouse|exit|error|click|dblclick|key|load|unload|change|move|submit|reset|cut|copy|select|start|stop)/i',
		];
		$replace = [
			'',
			'<d>\\1</d>',
			"on\n\\1",
		];

		return preg_replace($match, $replace, $input);
	}

	/**
	 * 删除代码中的换行符
	 * @param      $string
	 * @param bool $js
	 * @return mixed
	 */
	public static function trimEOL($string, $js = false)
	{
		$string = str_replace([chr(10), chr(13)], ['', ''], $string);

		return $js ? str_replace("'", "\'", $string) : $string;
	}

	/**
	 * 去除空格, 换行
	 * @param $string
	 * @return mixed
	 */
	public static function trimSpace($string)
	{
		$string = str_replace([chr(13), chr(10), "\n", "\r", "\t", '  '], ['', '', '', '', '', ''], $string);

		return $string;
	}

	/**
	 * 截取字符串
	 * @param   string $string 带截取的字符串
	 * @param   int    $length 长度
	 * @param string   $suffix 后缀
	 * @param int      $start  开始字符
	 * @return mixed|string 中文截断字符方法
	 */
	public static function cut($string, $length, $suffix = '', $start = 0, $strCode = 'utf-8')
	{
		if ($start) {
			$tmp    = self::cut($string, $start);
			$string = substr($string, strlen($tmp));
		}
		$strlen = strlen($string);
		if ($strlen <= $length) return $string;
		$string = str_replace(['&quot;', '&lt;', '&gt;'], ['"', '<', '>'], $string);
		$length = $length - strlen($suffix);
		$str    = '';
		if (strtolower($strCode) == 'utf-8') {
			$n = $tn = $noc = 0;
			while ($n < $strlen) {
				$t = ord($string[$n]);
				if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$tn = 1;
					$n++;
					$noc++;
				}
				elseif (194 <= $t && $t <= 223) {
					$tn  = 2;
					$n   += 2;
					$noc += 2;
				}
				elseif (224 <= $t && $t <= 239) {
					$tn  = 3;
					$n   += 3;
					$noc += 2;
				}
				elseif (240 <= $t && $t <= 247) {
					$tn  = 4;
					$n   += 4;
					$noc += 2;
				}
				elseif (248 <= $t && $t <= 251) {
					$tn  = 5;
					$n   += 5;
					$noc += 2;
				}
				elseif ($t == 252 || $t == 253) {
					$tn  = 6;
					$n   += 6;
					$noc += 2;
				}
				else {
					$n++;
				}
				if ($noc >= $length) break;
			}
			if ($noc > $length) $n -= $tn;
			$str = substr($string, 0, $n);
		}
		else {
			for ($i = 0; $i < $length; $i++) {
				$str .= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
			}
		}
		$str = str_replace(['"', '<', '>'], ['&quot;', '&lt;', '&gt;'], $str);

		return $str == $string ? $str : $str . $suffix;
	}

	/**
	 * 文字 -> 16进制表示
	 * @param $str
	 * @return string
	 */
	public static function toHex($str)
	{
		return bin2hex($str);
	}

	/**
	 * 16进制转换为字串
	 * @param $hex
	 * @return string
	 */
	public static function fromHex($hex)
	{
		// php5.4
		if (function_exists('hex2bin')) return hex2bin($hex);
		$str = '';
		for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
			$str .= chr(hexdec($hex[$i] . $hex[$i + 1]));
		}

		return $str;
	}

	/**
	 * 返回随机字串, 区分大小写
	 * @param        $length
	 * @param string $chars
	 * @return string
	 */
	public static function randomCustom($length, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz')
	{
		$hash = '';
		$max  = strlen($chars) - 1;
		for ($i = 0; $i < $length; $i++) {
			$hash .= $chars[mt_rand(0, $max)];
		}

		return $hash;
	}

	/**
	 * 随机ASCII字符
	 * @param int $length
	 * @return string
	 */
	public static function randomAscii($length = 8)
	{
		$str = '';
		for ($i = 0; $i < $length; $i++) {
			$str .= chr(mt_rand(33, 126));
		}

		return $str;
	}

	/**
	 * 获取一定范围内的随机数字 位数不足补零
	 * @param int $min 最小值
	 * @param int $max 最大值
	 * @return string
	 */
	public static function randomNumber($min, $max)
	{
		return sprintf('%0' . strlen($max) . 'd', mt_rand($min, $max));
	}

	/**
	 * 转换字符
	 * @param        $str
	 * @param string $fromCharset
	 * @param string $toCharset
	 * @return array|string
	 */
	public static function convert($str, $fromCharset = 'utf-8', $toCharset = 'gbk')
	{
		if (!$str) return '';
		$fromCharset = strtolower($fromCharset);
		$toCharset   = strtolower($toCharset);
		if ($fromCharset == $toCharset) return $str;
		$fromCharset = str_replace('gbk', 'gb2312', $fromCharset);
		$toCharset   = str_replace('gbk', 'gb2312', $toCharset);
		$fromCharset = str_replace('utf8', 'utf-8', $fromCharset);
		$toCharset   = str_replace('utf8', 'utf-8', $toCharset);

		if ($toCharset == 'utf-8' && self::isUtf8($str)) {
			return $str;
		}
		if ($toCharset == 'gbk' && !self::isUtf8($str)) {
			return $str;
		}
		if ($toCharset == $fromCharset) return $str;
		$tmp = [];
		if (function_exists('iconv')) {
			if (is_array($str)) {
				foreach ($str as $key => $val) {
					$tmp[$key] = iconv($fromCharset, $toCharset . '//IGNORE', $val);
				}

				return $tmp;
			}

			return iconv($fromCharset, $toCharset . '//IGNORE', $str);
		}
		elseif (function_exists('mb_convert_encoding')) {
			if (is_array($str)) {
				foreach ($str as $key => $val) {
					$tmp[$key] = mb_convert_encoding($val, $toCharset, $fromCharset);
				}

				return $tmp;
			}

			return mb_convert_encoding($str, $toCharset, $fromCharset);
		}

		return self::_convert($str, $toCharset, $fromCharset);
	}

	/**
	 * 批量转换
	 * @param        $str
	 * @param string $fromCharset
	 * @param string $toCharset
	 * @return array
	 */
	public static function batchConvert($str, $fromCharset = 'utf-8', $toCharset = 'gbk')
	{
		if (is_array($str)) {
			foreach ($str as $k => $v) {
				if (is_array($v)) {
					$str[$k] = self::batchConvert($v, $fromCharset, $toCharset);
				}
				else {
					$str[$k] = self::convert($v, $fromCharset, $toCharset);
				}
			}
		}

		return $str;
	}

	/**
	 * 中文->Utf8
	 * @param $char
	 * @return string
	 */
	public static function ch2Utf8($char)
	{
		$str = '';
		if ($char < 0x80) {
			$str .= $char;
		}
		elseif ($char < 0x800) {
			$str .= (0xC0 | $char >> 6);
			$str .= (0x80 | $char & 0x3F);
		}
		elseif ($char < 0x10000) {
			$str .= (0xE0 | $char >> 12);
			$str .= (0x80 | $char >> 6 & 0x3F);
			$str .= (0x80 | $char & 0x3F);
		}
		elseif ($char < 0x200000) {
			$str .= (0xF0 | $char >> 18);
			$str .= (0x80 | $char >> 12 & 0x3F);
			$str .= (0x80 | $char >> 6 & 0x3F);
			$str .= (0x80 | $char & 0x3F);
		}

		return $str;
	}

	/**
	 * 文本->拼音
	 * Str::text2py('大众',1)   =>  d
	 * @param      $chars
	 * @param int  $length      返回的拼音的长度, 以截取为准
	 * @param bool $firstLetter 是否返回首字母, 如 '天安门' => 'tam'
	 * @return string
	 */
	public static function text2py($chars, $length = 0, $firstLetter = false)
	{
		$pinyin = self::chars2py($chars, $firstLetter);
		if (!$length) {
			return implode('', $pinyin);
		}

		return substr(implode('', $pinyin), 0, $length);
	}

	/**
	 * 文本->拼音数组
	 * @param      $chars
	 * @param bool $firstLetter
	 * @return array
	 */
	public static function chars2py($chars, $firstLetter = false)
	{
		$chars  = self::chars2array($chars);
		$pinyin = [];
		foreach ($chars as $char) {
			$py = self::_quickChar2py($char);
			if (!$py) {
				$py = self::_slowChar2py($char);
			}
			$pinyin[] = $firstLetter ? $py[0] : $py;
		}

		return $pinyin;
	}

	/**
	 * 将文字分解为数组, 支持UTF8+英文, 不支持GBK
	 * @param $str
	 * @return array
	 */
	public static function chars2array($str)
	{
		$array = [];
		while (strlen($str) > 0) {
			$strTest = decbin(ord(substr($str, 0, 1)));
			$strTest = str_pad($strTest, 8, '0', STR_PAD_LEFT);
			$byteNum = 0;
			if (preg_match('/0[10]{7}/s', $strTest, $matches)) {
				$byteNum = 1;
			}
			elseif (preg_match('/110[10]{5}/s', $strTest, $matches)) {
				$byteNum = 2;
			}
			elseif (preg_match('/1110[10]{4}/s', $strTest, $matches)) {
				$byteNum = 3;
			}
			elseif (preg_match('/11110[10]{3}/s', $strTest, $matches)) {
				$byteNum = 4;
			}
			array_push($array, substr($str, 0, $byteNum));
			$str = substr($str, $byteNum);
		}

		return $array;
	}

	/**
	 * 计算字符长度
	 * @param $chars
	 * @return int
	 */
	public static function chnLength($chars)
	{
		return count(self::chars2array($chars));
	}

	/**
	 * 计算字符长度
	 * @param $string
	 * @return int
	 */
	public static function count($string)
	{
		$string = self::convert($string, 'utf-8', 'gbk');
		$length = strlen($string);
		$count  = 0;
		for ($i = 0; $i < $length; $i++) {
			$t = ord($string[$i]);
			if ($t > 127) $i++;
			$count++;
		}

		return $count;
	}

	/**
	 * 检测字符是否为UTF8编码
	 * @param $str
	 * @return int
	 */
	public static function isUtf8($str)
	{
		return preg_match('%^(?:
	          [\x09\x0A\x0D\x20-\x7E]            # ASCII
	        | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
	        |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
	        | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
	        |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
	        |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
	        | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
	        |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
	    )*$%xs', $str);
	}

	/**
	 * 菊花文生成
	 * @param $str
	 * @return string
	 */
	public static function chrysanthemum($str)
	{
		if (function_exists('mb_substr')) {
			mb_internal_encoding('UTF-8');
			$len = mb_strlen($str);
			$mb  = [];
			for ($i = 0; $i < $len; $i++) {
				$mb[] = mb_substr($str, $i, 1);
			}
			$mb[] = '';

			return implode('&#1161;', $mb);
		}

		return $str;
	}

	/**
	 * JS 转义函数
	 * @param $str
	 * @return string
	 */
	public static function jsEscape($str)
	{
		return addcslashes($str, "\\\'\"&\n\r<>");
	}

	/**
	 * 分割 separate, 去除空格
	 * @param        $str
	 * @param string $separator
	 * @return array
	 */
	public static function separate($separator, $str)
	{
		$str    = trim($str);
		$return = [];
		if ($str) {
			if (strpos($str, $separator) !== false) {
				$arrStr = explode($separator, $str);
				$return = array_map('trim', $arrStr);
			}
			else {
				$return = [$str];
			}
		}

		return $return;
	}

	/**
	 * 唯一的 表单ID值
	 * @param $prefix
	 * @return string
	 */
	public static function uniqueId($prefix)
	{
		return $prefix . '_' . self::random(4);
	}

	/**
	 * 获取配置
	 * @param $key
	 * @return string
	 */
	private static function _setting($key)
	{
		defined('LEMON_LIB_ATTACHMENT_PATH') or define('LEMON_LIB_ATTACHMENT_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'attachment' . DIRECTORY_SEPARATOR);
		$paths = [
			'gb-pinyin'  => __DIR__ . '/attachment/str_gb-pinyin.table',
			'gb-unicode' => __DIR__ . '/attachment/str_gb-unicode.table',
			'pinyin'     => __DIR__ . '/attachment/str_pinyin.table',
		];

		return isset($paths[$key]) ? $paths[$key] : '';
	}

	/**
	 * gbk编码字符转换到拼音, 快速匹配模式
	 * @param $text
	 * @return string
	 */
	private static function _quickChar2py($text)
	{
		if (!$text) return '';
		$text = self::convert($text, 'utf-8', 'gbk');
		$data = [];
		$tmp  = @file(self::_setting('gb-pinyin'));
		if (!$tmp) return '';
		$tmps = count($tmp);
		for ($i = 0; $i < $tmps; $i++) {
			$tmp1     = explode("\t", $tmp[$i]);
			$data[$i] = [$tmp1[0], $tmp1[1]];
		}
		$r       = [];
		$k       = 0;
		$textlen = strlen($text);
		for ($i = 0; $i < $textlen; $i++) {
			$p = ord(substr($text, $i, 1));
			if ($p > 160) {
				$q = ord(substr($text, ++$i, 1));
				$p = $p * 256 + $q - 65536;
			}
			if ($p > 0 && $p < 160) {
				$r[$k] = chr($p);
			}
			elseif ($p < -20319 || $p > -10247) {
				$r[$k] = '';
			}
			else {
				for ($j = $tmps - 1; $j >= 0; $j--) {
					if ($data[$j][1] <= $p) break;
				}
				$r[$k] = $data[$j][0];
			}
			$k++;
		}

		return implode('', $r);
	}

	/**
	 * 支持单字符拼音->文字
	 * @param $char
	 * @return bool
	 */
	private static function _slowChar2py($char)
	{
		$str = file_get_contents(self::_setting('pinyin'));
		if (preg_match("/{$char}([a-z ]{1,15})/is", $str, $match)) {
			return $match[1];
		}

		return false;
	}

	/**
	 * 字串转换函数
	 * @param        $str
	 * @param string $fromCharset
	 * @param string $toCharset
	 * @return string
	 */
	private static function _convert($str, $fromCharset = 'utf-8', $toCharset = 'gb2312')
	{
		$fromCharset = str_replace('utf-8', 'utf8', $fromCharset);
		$toCharset   = str_replace('utf-8', 'utf8', $toCharset);
		$tmp         = file(self::_setting('gb-unicode'));
		if (!$tmp) return $str;
		$table = [];
		while (list($key, $value) = each($tmp)) {
			if ($fromCharset == 'utf8') {
				$table[hexdec(substr($value, 7, 6))] = substr($value, 0, 6);
			}
			else {
				$table[hexdec(substr($value, 0, 6))] = substr($value, 7, 6);
			}
		}
		unset($tmp);
		$cStr = '';
		if ($fromCharset == 'utf8') {
			$len = strlen($str);
			$i   = 0;
			while ($i < $len) {
				$c = ord(substr($str, $i++, 1));
				switch ($c >> 4) {
					case 0:
					case 1:
					case 2:
					case 3:
					case 4:
					case 5:
					case 6:
					case 7:
						$cStr .= substr($str, $i - 1, 1);
						break;
					case 12:
					case 13:
						$char2 = ord(substr($str, $i++, 1));
						$char3 = $table[(($c & 0x1F) << 6) | ($char2 & 0x3F)];
						$cStr  .= self::fromHex(dechex($char3 + 0x8080));
						break;
					case 14:
						$char2 = ord(substr($str, $i++, 1));
						$char3 = ord(substr($str, $i++, 1));
						$char4 = $table[(($c & 0x0F) << 12) | (($char2 & 0x3F) << 6) | (($char3 & 0x3F) << 0)];
						$cStr  .= self::fromHex(dechex($char4 + 0x8080));
						break;
				}
			}
		}
		else {
			while ($str) {
				if (ord(substr($str, 0, 1)) > 127) {
					$utf8  = self::ch2Utf8(hexdec($table[hexdec(bin2hex(substr($str, 0, 2))) - 0x8080]));
					$dutf8 = strlen($utf8);
					for ($i = 0; $i < $dutf8; $i += 3) {
						$cStr .= chr(substr($utf8, $i, 3));
					}
					$str = substr($str, 2, strlen($str));
				}
				else {
					$cStr .= substr($str, 0, 1);
					$str  = substr($str, 1, strlen($str));
				}
			}
		}
		unset($table);

		return $cStr;
	}

	/**
	 * 解析 a|1;b|2  样式的字串到数组
	 * @param $str
	 * @return array
	 */
	public static function parseKey($str)
	{
		if (!$str) {
			return [];
		}
		if (is_array($str)) {
			return $str;
		}
		if ($str instanceof Arrayable) {
			return $str->toArray();
		}
		$arr = explode(';', $str);
		if ($arr) {
			$return = [];
			foreach ($arr as $v) {
				if ($v && strpos($v, '|') !== false) {
					list($key, $value) = explode('|', $v);
					$key          = trim($key);
					$return[$key] = trim($value);
				}
			}

			return $return;
		}

		return $arr;
	}

	/**
	 * sql against encode
	 * @param $ids
	 * @return string
	 */
	public static function matchEncode($ids)
	{
		if (!is_array($ids)) {
			$ids = explode(',', $ids);
		}

		return ',_' . implode('_,_', $ids) . '_,';
	}

	/**
	 * reverse for match
	 * @param            $ids
	 * @param bool|false $array
	 * @return array|mixed
	 */
	public static function matchDecode($ids, $array = false)
	{
		$ids = trim($ids, ',_');
		$ids = trim($ids, '_,');
		if ($array) {
			if (strpos($ids, '_,_') !== false) {
				return explode('_,_', $ids);
			}

			return [];
		}

		return str_replace('_,_', ',', $ids);
	}

	/**
	 * 隐藏联系方式
	 * @param $input
	 * @return mixed|string
	 */
	public static function hideContact($input)
	{
		if ($input) {
			return substr_replace($input, '****', 3, -4);
		}

		return '';
	}

	/**
	 * 隐藏邮箱
	 * @param $input
	 * @return mixed|string
	 */
	public static function hideEmail($input)
	{
		if ($input) {
			return substr_replace($input, '****', 3, strpos($input, '@') - 3);
		}

		return '';
	}

	/**
	 * Converts number to its ordinal English form.
	 *
	 * This method converts 13 to 13th, 2 to 2nd ...
	 *
	 * @param int $number Number to get its ordinal value
	 * @return string ordinal representation of given string
	 */
	public static function ordinal($number)
	{
		if (in_array(($number % 100), range(11, 13))) {
			return $number . 'th';
		}

		switch (($number % 10)) {
			case 1:
				return $number . 'st';
			case 2:
				return $number . 'nd';
			case 3:
				return $number . 'rd';
			default:
				return $number . 'th';
		}
	}

	/**
	 * Converts line breaks to a standard \r\n pattern.
	 * @param $string
	 * @return mixed
	 */
	public static function normalizeEol($string)
	{
		return preg_replace('~\R~u', PHP_EOL, $string);
	}

	/**
	 * Removes the starting slash from a class namespace \
	 * @param $name
	 * @return string
	 */
	public static function normalizeClassName($name)
	{
		if (is_object($name)) {
			$name = get_class($name);
		}

		$name = '\\' . ltrim($name, '\\');

		return $name;
	}

	/**
	 * 从对象或者类名中生成 class id
	 * @param $name
	 * @return string
	 */
	public static function getClassId($name)
	{
		if (is_object($name))
			$name = get_class($name);

		$name = ltrim($name, '\\');
		$name = str_replace('\\', '_', $name);

		return strtolower($name);
	}

	/**
	 * 返回一个类的命名空间
	 * @param $name
	 * @return bool|string
	 */
	public static function getClassNamespace($name)
	{
		$name = static::normalizeClassName($name);

		return substr($name, 0, strrpos($name, '\\'));
	}

	/**
	 * 清除链接
	 * @param $content
	 * @return mixed
	 */
	public static function clearLink($content)
	{
		$content = preg_replace('/<a[^>]*>/i', '', $content);

		return preg_replace("/<\/a>/i", '', $content);
	}

	/**
	 * 完善链接
	 * @param $url
	 * @return string
	 */
	public static function fixLink($url)
	{
		if (strlen($url) < 10) return '';

		return strpos($url, '://') === false ? 'http://' . $url : $url;
	}

	/**
	 * 将内容截取到介绍中
	 * @param string $content 有待截取的内容
	 * @param int    $length  带截取的长度
	 * @return mixed|string 截取内容的一部分
	 */
	public static function intro($content, $length = 0)
	{
		if ($length) {
			$content = str_replace([' ', '[pagebreak]'], ['', ''], $content);
			$intro   = trim(self::trimEOL(strip_tags($content)));
			// 删除实体
			$intro = preg_replace('/&([a-z]{1,});/', '', $intro);

			return nl2br(self::cut($intro, $length, '...'));
		}

		return '';
	}

	/**
	 * 返回唯一的值
	 * @param string $current
	 * @param string $str
	 * @param string $delimiter
	 * @param bool   $remove
	 * @return mixed
	 */
	public static function unique($current, $str, $delimiter = ',', $remove = false)
	{
		if (!$remove) {
			// 追加
			$current .= $delimiter . $str;
		}

		// 去重
		$arr = explode($delimiter, $current);
		return collect($arr)->unique()->filter(function ($item) use ($remove, $str) {
			return $remove ? ($item && $item != $str) : $item;
		})->implode($delimiter);
	}


	/**
	 * 获取 markdown 索引
	 * @param $content
	 * @return array
	 */
	public function mdToc($content): array
	{
		// ensure using only "\n" as line-break
		$source  = str_replace(["\r\n", "\r"], "\n", $content);
		$raw_toc = [];
		// look for markdown TOC items
		preg_match_all(
			'/^(?:=|-|#).*$/m',
			$source,
			$matches,
			PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE
		);

		// pre process: iterate matched lines to create an array of items
		// where each item is an array(level, text)
		$file_size = strlen($source);
		foreach ($matches[0] as $item) {
			$found_mark = substr($item[0], 0, 1);
			if ($found_mark == '#') {
				// text is the found item
				$item_text  = $item[0];
				$item_level = strrpos($item_text, '#') + 1;
				$item_text  = substr($item_text, $item_level);
			}
			else {
				// text is the previous line (empty if <hr>)
				$item_offset      = $item[1];
				$prev_line_offset = strrpos($source, "\n", -($file_size - $item_offset + 2));
				$item_text        =
					substr($source, $prev_line_offset, $item_offset - $prev_line_offset - 1);
				$item_text        = trim($item_text);
				$item_level       = $found_mark == '=' ? 1 : 2;
			}
			if (!trim($item_text) or strpos($item_text, '|') !== false) {
				// item is an horizontal separator or a table header, don't mind
				continue;
			}
			$raw_toc[] = [
				'level' => $item_level,
				'text'  => trim($item_text),
			];
		}

		// create a JSON list (the easiest way to generate HTML structure is using JS)
		return $raw_toc;
	}
}