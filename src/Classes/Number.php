<?php namespace Poppy\Framework\Classes;

/**
 * This file is part of the Moontoast\Math library
 *
 * Copyright 2013 Moontoast, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @copyright 2013 Moontoast, Inc.
 * @license   http://alphabase.moontoast.com/licenses/apache-2.0.txt Apache 2.0
 */

use InvalidArgumentException;
use Poppy\Framework\Exceptions\ArithmeticException;

/**
 * Represents a number for use with Binary Calculator computations
 *
 * @link http://www.php.net/bcmath
 */
class Number
{
	/**
	 * Number value, as a string
	 *
	 * @var string
	 */
	protected $numberValue;

	/**
	 * The scale for the current number
	 *
	 * @var int
	 */
	protected $numberScale;

	/**
	 * Constructs a BigNumber object from a string, integer, float, or any
	 * object that may be cast to a string, resulting in a numeric string value
	 *
	 * @param mixed $number May be of any type that can be cast to a string
	 *                      representation of a base 10 number
	 * @param int   $scale  (optional) Specifies the default number of digits after the decimal
	 *                      place to be used in operations for this BigNumber
	 */
	public function __construct($number, $scale = 2)
	{
		if ($scale) {
			$this->setScale($scale);
		}

		$this->setValue($number);
	}

	/**
	 * Returns the string value of this BigNumber
	 *
	 * @return string String representation of the number in base 10
	 */
	public function __toString()
	{
		return (string) $this->getValue();
	}

	/**
	 * 将当前数字设置为自身的绝对值
	 * Sets the current number to the absolute value of itself
	 */
	public function abs()
	{
		// Use substr() to find the negative sign at the beginning of the
		// number, rather than using signum() to determine the sign.
		if (substr($this->numberValue, 0, 1) === '-') {
			$this->numberValue = substr($this->numberValue, 1);
		}

		return $this;
	}

	/**
	 * 加法运算
	 * Adds the given number to the current number
	 *
	 * @param mixed $number May be of any type that can be cast to a string
	 *                      representation of a base 10 number
	 * @link http://www.php.net/bcadd
	 * @return $this
	 */
	public function add($number)
	{
		$this->numberValue = bcadd(
			$this->numberValue,
			$this->filterNumber($number),
			$this->getScale()
		);

		return $this;
	}

	/**
	 * 通过重新排列当前数字来查找下一个最高整型值
	 * Finds the next highest integer value by rounding up the current number
	 * if necessary
	 *
	 * @link http://www.php.net/ceil
	 */
	public function ceil()
	{
		$number = $this->getValue();

		if ($this->isPositive()) {
			// 14 is the magic precision number
			$number = bcadd($number, '0', 14);
			if (substr($number, -15) != '.00000000000000') {
				$number = bcadd($number, '1', 0);
			}
		}

		$this->numberValue = bcadd($number, '0', 0);

		return $this;
	}

	/**
	 * 比较数据
	 * Compares the current number with the given number
	 *
	 * Returns 0 if the two operands are equal, 1 if the current number is
	 * larger than the given number, -1 otherwise.
	 *
	 * @param mixed $number May be of any type that can be cast to a string
	 *                      representation of a base 10 number
	 * @return int
	 * @link http://www.php.net/bccomp
	 */
	public function compareTo($number)
	{
		return bccomp(
			$this->numberValue,
			$this->filterNumber($number),
			$this->getScale()
		);
	}

	/**
	 * 进制转换, 转换成任意进制
	 * Returns the current value converted to an arbitrary base
	 *
	 * @param int $base The base to convert the current number to
	 * @return string String representation of the number in the given base
	 */
	public function convertToBase($base)
	{
		return self::convertFromBase10($this->getValue(), $base);
	}

	/**
	 * 将当前数字的值减一
	 * Decreases the value of the current number by one
	 */
	public function decrement()
	{
		return $this->subtract(1);
	}

	/**
	 * 除法运算
	 * Divides the current number by the given number
	 *
	 * @param mixed $number May be of any type that can be cast to a string
	 *                      representation of a base 10 number
	 * @return $this
	 * @throws ArithmeticException if $number is zero
	 * @link http://www.php.net/bcdiv
	 */
	public function divide($number)
	{
		$number = $this->filterNumber($number);

		if ($number == '0') {
			throw new ArithmeticException('Division by zero');
		}

		$this->numberValue = bcdiv(
			$this->numberValue,
			$number,
			$this->getScale()
		);

		return $this;
	}

	/**
	 * 通过舍入当前数字找到下一个最低整数值
	 * Finds the next lowest integer value by rounding down the current number
	 * if necessary
	 *
	 * @link http://www.php.net/floor
	 */
	public function floor()
	{
		$number = $this->getValue();

		if ($this->isNegative()) {
			// 14 is the magic precision number
			$number = bcadd($number, '0', 14);
			if (substr($number, -15) != '.00000000000000') {
				$number = bcsub($number, '1', 0);
			}
		}

		$this->numberValue = bcadd($number, '0', 0);

		return $this;
	}

	/**
	 * 返回小数位数
	 * Returns the scale used for this BigNumber
	 *
	 * If no scale was set, this will default to the value of bcmath.scale
	 * in php.ini.
	 *
	 * @return int
	 */
	public function getScale()
	{
		if ($this->numberScale === null) {
			return ini_get('bcmath.scale');
		}

		return $this->numberScale;
	}

	/**
	 * 获取字串值
	 * Returns the current raw value of this BigNumber
	 *
	 * @return string String representation of the number in base 10
	 */
	public function getValue()
	{
		return $this->numberValue;
	}

	/**
	 * 增加
	 * Increases the value of the current number by one
	 *
	 */
	public function increment()
	{
		return $this->add(1);
	}

	/**
	 * 检测是否相等
	 * Returns true if the current number equals the given number
	 *
	 * @param mixed $number May be of any type that can be cast to a string
	 *                      representation of a base 10 number
	 * @return bool
	 */
	public function isEqualTo($number)
	{
		return $this->compareTo($number) == 0;
	}

	/**
	 * 检测是否大于
	 * Returns true if the current number is greater than the given number
	 *
	 * @param mixed $number May be of any type that can be cast to a string
	 *                      representation of a base 10 number
	 * @return bool
	 */
	public function isGreaterThan($number)
	{
		return $this->compareTo($number) == 1;
	}

	/**
	 * 检测大于等于
	 * Returns true if the current number is greater than or equal to the given number
	 *
	 * @param mixed $number May be of any type that can be cast to a string
	 *                      representation of a base 10 number
	 * @return bool
	 */
	public function isGreaterThanOrEqualTo($number)
	{
		return $this->compareTo($number) >= 0;
	}

	/**
	 * 检测小与
	 * Returns true if the current number is less than the given number
	 *
	 * @param mixed $number May be of any type that can be cast to a string
	 *                      representation of a base 10 number
	 * @return bool
	 */
	public function isLessThan($number)
	{
		return $this->compareTo($number) == -1;
	}

	/**
	 * 检测小与等于
	 * Returns true if the current number is less than or equal to the given number
	 *
	 * @param mixed $number May be of any type that can be cast to a string
	 *                      representation of a base 10 number
	 * @return bool
	 */
	public function isLessThanOrEqualTo($number)
	{
		return $this->compareTo($number) <= 0;
	}

	/**
	 * 是否是负数
	 * Returns true if the current number is a negative number
	 *
	 * @return bool
	 */
	public function isNegative()
	{
		return $this->signum() == -1;
	}

	/**
	 * 是否是正数
	 * Returns true if the current number is a positive number
	 *
	 * @return bool
	 */
	public function isPositive()
	{
		return $this->signum() == 1;
	}

	/**
	 * 取余数
	 * Finds the modulus of the current number divided by the given number
	 *
	 * @param mixed $number May be of any type that can be cast to a string
	 *                      representation of a base 10 number
	 * @return $this
	 * @throws ArithmeticException if $number is zero
	 * @link http://www.php.net/bcmod
	 */
	public function mod($number)
	{
		$number = $this->filterNumber($number);

		if ($number == '0') {
			throw new ArithmeticException('Division by zero');
		}

		$this->numberValue = bcmod(
			$this->numberValue,
			$number
		);

		return $this;
	}

	/**
	 * 乘法
	 * Multiplies the current number by the given number
	 *
	 * @param mixed $number May be of any type that can be cast to a string
	 *                      representation of a base 10 number
	 * @link http://www.php.net/bcmul
	 * @return $this
	 */
	public function multiply($number)
	{
		$this->numberValue = bcmul(
			$this->numberValue,
			$this->filterNumber($number),
			$this->getScale()
		);

		return $this;
	}

	/**
	 * 返回负数
	 * Sets the current number to the negative value of itself
	 */
	public function negate()
	{
		return $this->multiply(-1);
	}

	/**
	 * 幂运算
	 * Raises current number to the given number
	 *
	 * @param mixed $number May be of any type that can be cast to a string
	 *                      representation of a base 10 number
	 * @link http://www.php.net/bcpow
	 * @return $this
	 */
	public function pow($number)
	{
		$this->numberValue = bcpow(
			$this->numberValue,
			$this->filterNumber($number),
			$this->getScale()
		);

		return $this;
	}

	/**
	 * 指定次数的幂运算
	 * Raises the current number to the $pow, then divides by the $mod
	 * to find the modulus
	 *
	 * This is functionally equivalent to the following code:
	 *
	 * <code>
	 *     $n = new BigNumber(1234);
	 *     $n->mod($n->pow(32), 2);
	 * </code>
	 *
	 * However, it uses bcpowmod(), so it is faster and can accept larger
	 * parameters.
	 *
	 * @param mixed $pow May be of any type that can be cast to a string
	 *                   representation of a base 10 number
	 * @param mixed $mod May be of any type that can be cast to a string
	 *                   representation of a base 10 number
	 * @return $this
	 * @throws ArithmeticException if $number is zero
	 * @link http://www.php.net/bcpowmod
	 */
	public function powMod($pow, $mod)
	{
		$mod = $this->filterNumber($mod);

		if ($mod === '0') {
			throw new ArithmeticException('Division by zero');
		}

		$this->numberValue = bcpowmod(
			$this->numberValue,
			$this->filterNumber($pow),
			$mod,
			$this->getScale()
		);

		return $this;
	}

	/**
	 * 四舍五入
	 * Rounds the current number to the nearest integer
	 *
	 * @param int $precision
	 * @return Number
	 */
	public function round($precision = 0): self
	{
		$original = $this->getValue();
		$floored  = $this->floor()->getValue();
		$diff     = bcsub($original, $floored, 20);
		if ($this->isNegative()) {
			$roundedDiff = round($diff, $precision, PHP_ROUND_HALF_DOWN);
		}
		else {
			$roundedDiff = round($diff, $precision);
		}

		$this->numberValue = bcadd(
			$floored,
			$roundedDiff,
			$precision
		);

		return $this;
	}

	/**
	 * 设置小数位数
	 * Sets the scale of this BigNumber
	 *
	 * @param int $scale Specifies the default number of digits after the decimal
	 *                   place to be used in operations for this BigNumber
	 * @return $this
	 */
	public function setScale($scale)
	{
		$this->numberScale = (int) $scale;

		return $this;
	}

	/**
	 * 设置为一个新值
	 * Sets the value of this BigNumber to a new value
	 *
	 * @param mixed $number May be of any type that can be cast to a string
	 *                      representation of a base 10 number
	 * @return $this
	 */
	public function setValue($number)
	{
		// Set the scale for the number to the scale value passed in
		$number = bcadd(
			$this->filterNumber($number),
			'0',
			$this->getScale()
		);

		$this->numberValue = $number;

		return $this;
	}

	/**
	 * 位向左移动
	 * Shifts the current number $bits to the left
	 *
	 * @param int $bits
	 * @return $this
	 */
	public function shiftLeft($bits)
	{
		$this->numberValue = bcmul(
			$this->numberValue,
			bcpow('2', $bits)
		);

		return $this;
	}

	/**
	 * 位向右移动
	 * Shifts the current number $bits to the right
	 *
	 * @param int $bits
	 * @return $this
	 */
	public function shiftRight($bits)
	{
		$this->numberValue = bcdiv(
			$this->numberValue,
			bcpow('2', $bits)
		);

		return $this;
	}

	/**
	 * 前数字的符号
	 * Returns the sign (signum) of the current number
	 *
	 * @return int -1, 0 or 1 as the value of this BigNumber is negative, zero or positive
	 */
	public function signum()
	{
		if ($this->isGreaterThan(0)) {
			return 1;
		}
		if ($this->isLessThan(0)) {
			return -1;
		}

		return 0;
	}

	/**
	 * 求平方根
	 * Finds the square root of the current number
	 *
	 * @link http://www.php.net/bcsqrt
	 */
	public function sqrt()
	{
		$this->numberValue = bcsqrt(
			$this->numberValue,
			$this->getScale()
		);

		return $this;
	}

	/**
	 * 减法
	 * Subtracts the given number from the current number
	 *
	 * @param mixed $number May be of any type that can be cast to a string
	 *                      representation of a base 10 number
	 * @link http://www.php.net/bcsub
	 * @return $this
	 */
	public function subtract($number)
	{
		$this->numberValue = bcsub(
			$this->numberValue,
			$this->filterNumber($number),
			$this->getScale()
		);

		return $this;
	}

	/**
	 * 转换成 String 类型
	 * Filters a number, converting it to a string value
	 *
	 * @param mixed $number
	 * @return string
	 */
	protected function filterNumber($number): string
	{
		return filter_var(
			$number,
			FILTER_SANITIZE_NUMBER_FLOAT,
			FILTER_FLAG_ALLOW_FRACTION
		);
	}

	/**
	 * 进制转换
	 * Converts a number between arbitrary bases (from 2 to 36)
	 *
	 * @param string|int $number   The number to convert
	 * @param int        $fromBase (optional) The base $number is in; defaults to 10
	 * @param int        $toBase   (optional) The base to convert $number to; defaults to 16
	 * @return string
	 */
	public static function baseConvert($number, $fromBase = 10, $toBase = 16)
	{
		$number = self::convertToBase10($number, $fromBase);

		return self::convertFromBase10($number, $toBase);
	}

	/**
	 * 转换 10 进制到其他进制
	 * Converts a base-10 number to an arbitrary base (from 2 to 36)
	 *
	 * @param string|int $number The number to convert
	 * @param int        $toBase The base to convert $number to
	 * @return string
	 * @throws InvalidArgumentException if $toBase is outside the range 2 to 36
	 */
	public static function convertFromBase10($number, $toBase)
	{
		if ($toBase < 2 || $toBase > 36) {
			throw new InvalidArgumentException("Invalid `to base' ({$toBase})");
		}

		$bn               = new self($number);
		$number           = $bn->abs()->getValue();
		$digits           = '0123456789abcdefghijklmnopqrstuvwxyz';
		$outNumber        = '';
		$returnDigitCount = 0;

		while (bcdiv($number, bcpow($toBase, (string) $returnDigitCount)) > ($toBase - 1)) {
			$returnDigitCount++;
		}

		for ($i = $returnDigitCount; $i >= 0; $i--) {
			$pow       = bcpow($toBase, (string) $i);
			$c         = bcdiv($number, $pow);
			$number    = bcsub($number, bcmul($c, $pow));
			$outNumber .= $digits[(int) $c];
		}

		return $outNumber;
	}

	/**
	 * 转换成 10 进制
	 * Converts a number from an arbitrary base (from 2 to 36) to base 10
	 *
	 * @param string|int $number   The number to convert
	 * @param int        $fromBase The base $number is in
	 * @return string
	 * @throws InvalidArgumentException if $fromBase is outside the range 2 to 36
	 */
	public static function convertToBase10($number, $fromBase)
	{
		if ($fromBase < 2 || $fromBase > 36) {
			throw new InvalidArgumentException("Invalid `from base' ({$fromBase})");
		}

		$number    = (string) $number;
		$len       = strlen($number);
		$base10Num = '0';

		for ($i = $len; $i > 0; $i--) {
			$c = ord($number[$len - $i]);

			if ($c >= ord('0') && $c <= ord('9')) {
				$c -= ord('0');
			}
			elseif ($c >= ord('A') && $c <= ord('Z')) {
				$c -= ord('A') - 10;
			}
			elseif ($c >= ord('a') && $c <= ord('z')) {
				$c -= ord('a') - 10;
			}
			else {
				continue;
			}

			if ($c >= $fromBase) {
				continue;
			}

			$base10Num = bcadd(bcmul($base10Num, $fromBase), (string) $c);
		}

		return $base10Num;
	}

	/**
	 * 设置默认小数位数
	 * Changes the default scale used by all Binary Calculator functions
	 *
	 * @param int $scale
	 * @return void
	 */
	public static function setDefaultScale($scale)
	{
		ini_set('bcmath.scale', $scale);
	}
}