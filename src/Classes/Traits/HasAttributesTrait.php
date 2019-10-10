<?php namespace Poppy\Framework\Classes\Traits;

/**
 * Trait HasAttributes.
 */
trait HasAttributesTrait
{
	/**
	 * attributes
	 * @var array $attributes
	 */
	protected $attributes;

	/**
	 * Get the instance as an array.
	 * @return array
	 */
	public function toArray()
	{
		return $this->attributes;
	}

	/**
	 * get
	 * @param string $offset  offset
	 * @param null   $default default
	 * @return null
	 */
	public function get(string $offset, $default = null)
	{
		return data_get($this->attributes, $offset, $default);
	}

	/**
	 * Whether a offset exists.
	 * @param mixed $offset offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return isset($this->attributes[$offset]);
	}

	/**
	 * Offset to retrieve.
	 * @param mixed $offset offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->attributes[$offset];
	}

	/**
	 * Offset to set.
	 * @param mixed $offset offset
	 * @param mixed $value  value
	 */
	public function offsetSet($offset, $value)
	{
		$this->attributes[$offset] = $value;
	}

	/**
	 * Offset to unset.
	 * @param mixed $offset offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->attributes[$offset]);
	}

	/**
	 * Specify data which should be serialized to JSON.
	 * @return array
	 */
	public function jsonSerialize()
	{
		return $this->attributes;
	}
}
