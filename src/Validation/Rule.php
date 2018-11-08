<?php namespace Poppy\Framework\Validation;

use Illuminate\Validation\Rule as IlluminateRule;

/**
 * Class Rule.
 */
class Rule extends IlluminateRule
{
	/**
	 * @return string
	 */
	public static function array()
	{
		return 'array';
	}

	/**
	 * 验证的字段必须完全是字母的字符
	 * @return string
	 */
	public static function alpha()
	{
		return 'alpha';
	}

	/**
	 * 验证的字段可能具有字母、数字、破折号（ - ）以及下划线（ _ ）
	 * @return string
	 */
	public static function alphaDash()
	{
		return 'alpha_dash';
	}

	/**
	 * string rule
	 * @return string
	 */
	public static function string()
	{
		return 'string';
	}

	/**
	 * 身份证号
	 * string rule
	 * @return string
	 */
	public static function chid()
	{
		return 'chid';
	}

	/**
	 * size
	 * @param $length
	 * @return string
	 */
	public static function size($length)
	{
		return 'size:' . $length;
	}

    /**
     * max
     * @param $length
     * @return string
     */
    public static function max($length)
    {
        return 'max:' . $length;
    }

	/**
	 * @return string
	 */
	public static function boolean()
	{
		return 'boolean';
	}

	/**
	 * @param $format
	 * @return string
	 */
	public static function dateFormat($format)
	{
		return 'date_format:' . $format;
	}

	/**
	 * @return string
	 */
	public static function date()
	{
		return 'date';
	}

	/**
	 * @return string
	 */
	public static function nullable()
	{
		return 'nullable';
	}

	/**
	 * @return string
	 */
	public static function email()
	{
		return 'email';
	}

	/**
	 * @return string
	 */
	public static function file()
	{
		return 'file';
	}

	/**
	 * @return string
	 */
	public static function image()
	{
		return 'image';
	}

	/**
	 * @param array $mimeTypes
	 * @return string
	 */
	public static function mimetypes(array $mimeTypes)
	{
		return 'mimetypes:' . implode(',', $mimeTypes);
	}

	/**
	 * @return string
	 */
	public static function numeric()
	{
		return 'numeric';
	}

	/**
	 * @param $regex
	 * @return string
	 */
	public static function regex($regex)
	{
		return 'regex:' . $regex;
	}

	/**
	 * @return string
	 */
	public static function required()
	{
		return 'required';
	}

	public static function confirmed()
	{
		return 'confirmed';
	}

	/**
	 * @return string
	 */
	public static function mobile()
	{
		return 'mobile';
	}

	/**
	 * @return string
	 */
	public static function password()
	{
		return 'password';
	}

	/**
	 * @return string
	 */
	public static function url()
	{
		return 'url';
	}

	/**
	 * Between String
	 * @param $start
	 * @param $end
	 * @return string
	 */
	public static function between($start, $end)
	{
		return 'between:' . $start . ',' . $end;
	}

	/**
	 * 最小数
	 * @param $value
	 * @return string
	 */
	public static function min($value)
	{
		return 'min:' . $value;
	}

	/**
	 * @return string
	 */
	public static function integer()
	{
		return 'integer';
	}
}
