<?php

namespace Poppy\Framework\Helper;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

/**
 * 字串处理
 */
class StrHelper
{
    /**
     * 获取 markdown 索引
     * @param string $content content
     * @return array
     */
    public function mdToc(string $content): array
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
            if ($found_mark === '#') {
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
                $item_level       = $found_mark === '=' ? 1 : 2;
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

    /**
     * 获取文件名后缀名
     * @param string $string string
     * @param string $split split
     * @return string
     */
    public static function suffix(string $string, $split = '.')
    {
        return strtolower(trim(substr(strrchr($string, $split), 1)));
    }

    /**
     * 获取中杠线分割的单词
     * @param string $string
     * @return array|string|string[]
     */
    public static function slug(string $string)
    {
        return str_replace(['_'], ['-'], Str::snake($string));
    }

    /**
     * 获取文件名前缀
     * @param string $string string
     * @param string $split string
     * @return string
     */
    public static function prefix(string $string, $split = '.')
    {
        return strtolower(trim(substr($string, 0, strpos($string, $split))));
    }

    /**
     * 检测是否含有空格符
     * @param string $value value
     * @return int
     */
    public static function hasSpace(string $value)
    {
        return preg_match('/\s+/', $value);
    }

    /**
     * 取消转义
     * @param mixed $input input
     * @return array|string
     */
    public static function stripSlashes($input)
    {
        return is_array($input) ? array_map([__CLASS__, __FUNCTION__], $input) : stripslashes($input);
    }

    /**
     * 转义操作
     * @param mixed $input input
     * @return array|string
     */
    public static function addSlashes($input)
    {
        return is_array($input) ? array_map([__CLASS__, __FUNCTION__], $input) : addslashes($input);
    }

    /**
     * 转义特殊字符
     * @param mixed $input input
     * @param bool $preserveAmpersand preserveAmpersand
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
     * @param mixed $input input
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
     * @param string $string string
     * @param bool $js js
     * @return mixed
     */
    public static function trimEOL(string $string, $js = false)
    {
        $string = str_replace([chr(10), chr(13)], '', $string);

        return $js ? str_replace("'", "\'", $string) : $string;
    }

    /**
     * 去除空格, 换行
     * @param string $string string
     * @return string
     */
    public static function trimSpace(string $string): string
    {
        $string = str_replace([chr(13), chr(10), "\n", "\r", "\t", '  '], '', $string);

        return $string;
    }

    /**
     * 截取字符串
     * @param string $string 带截取的字符串
     * @param int $length 长度
     * @param string $suffix 后缀
     * @param int $start 开始字符
     * @param string $char_code
     * @return mixed|string 中文截断字符方法
     */
    public static function cut(string $string, int $length, $suffix = '', $start = 0, $char_code = 'utf-8')
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
        if (strtolower($char_code) == 'utf-8') {
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

        return $str === $string ? $str : $str . $suffix;
    }

    /**
     * 文字 -> 16进制表示
     * @param string $str str
     * @return string
     */
    public static function toHex(string $str)
    {
        return bin2hex($str);
    }

    /**
     * 16进制转换为字串
     * @param string $hex hex
     * @return string
     */
    public static function fromHex(string $hex)
    {
        // php5.4
        if (function_exists('hex2bin')) {
            return hex2bin($hex);
        }
        $str = '';
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $str .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }

        return $str;
    }

    /**
     * 返回随机字串, 区分大小写
     * @param int $length length
     * @param string $chars chars
     * @return string
     */
    public static function randomCustom(int $length, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz')
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
     * @param int $length length
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
    public static function randomNumber(int $min, int $max)
    {
        return sprintf('%0' . strlen($max) . 'd', mt_rand($min, $max));
    }

    /**
     * 转换字符
     * @param string $str str
     * @param string $fromCharset fromCharset
     * @param string $toCharset toCharset
     * @return array|string
     */
    public static function convert(string $str, $fromCharset = 'utf-8', $toCharset = 'gbk')
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
        if (is_array($str)) {
            foreach ($str as $key => $val) {
                $tmp[$key] = mb_convert_encoding($val, $toCharset, $fromCharset);
            }

            return $tmp;
        }

        return mb_convert_encoding($str, $toCharset, $fromCharset);
    }

    /**
     * 批量转换
     * @param mixed $str str
     * @param string $fromCharset fromCharset
     * @param string $toCharset toCharset
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
     * @param string $char char
     * @return string
     */
    public static function ch2Utf8(string $char)
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
     * 计算字符长度
     * @param mixed $string string
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
     * @param string $str str
     * @return int
     */
    public static function isUtf8(string $str)
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
     * @param string $str str
     * @return string
     */
    public static function chrysanthemum(string $str)
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
     * @param string $str str
     * @return string
     */
    public static function jsEscape(string $str)
    {
        return addcslashes($str, "\\\'\"&\n\r<>");
    }

    /**
     * 分割 separate, 去除空格
     * @param string $str str
     * @param string $separator separator
     * @return array
     */
    public static function separate(string $separator, string $str)
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
     * 解析 a|1;b|2  样式的字串到数组
     * @param mixed $str str
     * @return mixed
     */
    public static function parseKey($str)
    {
        if (!$str) {
            return [];
        }
        if (is_object($str) || is_array($str)) {
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
                    [$key, $value] = explode('|', $v);
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
     * @param mixed $ids ids
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
     * @param mixed $ids ids
     * @param bool|false $array array
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
     * @param string $input input
     * @return string
     */
    public static function hideContact(string $input): string
    {
        if ($input) {
            return str_replace(mb_substr($input, 3, -4), '****', $input);
        }
        return '';
    }

    /**
     * 隐藏邮箱
     * @param string $input input
     * @return string
     */
    public static function hideEmail(string $input): string
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
    public static function ordinal(int $number)
    {
        if (in_array($number % 100, range(11, 13), true)) {
            return $number . 'th';
        }

        switch ($number % 10) {
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
     * @param string $string string
     * @return mixed
     */
    public static function normalizeEol(string $string)
    {
        return preg_replace('~\R~u', PHP_EOL, $string);
    }

    /**
     * Removes the starting slash from a class namespace \
     * @param mixed $name name
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
     * @param mixed $name name
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
     * @param string $name name
     * @return bool|string
     */
    public static function getClassNamespace(string $name)
    {
        $name = static::normalizeClassName($name);

        return substr($name, 0, strrpos($name, '\\'));
    }

    /**
     * 清除链接
     * @param string $content content
     * @return mixed
     */
    public static function clearLink(string $content)
    {
        $content = preg_replace('/<a[^>]*>/i', '', $content);

        return preg_replace("/<\/a>/i", '', $content);
    }

    /**
     * 完善链接
     * @param string $url url
     * @return string
     */
    public static function fixLink(string $url)
    {
        if (strlen($url) < 10) return '';

        return strpos($url, '://') === false ? 'http://' . $url : $url;
    }

    /**
     * 将内容截取到介绍中
     * @param string $content 有待截取的内容
     * @param int $length 带截取的长度
     * @return mixed|string 截取内容的一部分
     */
    public static function intro(string $content, $length = 0)
    {
        if ($length) {
            $content = str_replace([' ', '[pagebreak]'], ['', ''], $content);
            $intro   = trim(self::trimEOL(strip_tags($content)));
            // 删除实体
            $intro = preg_replace('/&([a-z]+);/', '', $intro);

            return nl2br(self::cut($intro, $length, '...'));
        }

        return '';
    }

    /**
     * 格式化ID
     * @param string|string[] $string
     * @return string|string[]
     */
    public static function formatId($string)
    {
        return str_replace('.', '_', $string);
    }

    /**
     * 返回唯一的值
     * @param string $current current
     * @param string $str str
     * @param string $delimiter delimiter
     * @param bool $remove remove
     * @return mixed
     */
    public static function unique(string $current, string $str, $delimiter = ',', $remove = false)
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
}