<?php

namespace Poppy\MgrPage\Classes\Form\Traits;

trait PlainInput
{
	protected $prepend;

	protected $append;

	public function prepend($string)
	{
		if (is_null($this->prepend)) {
			$this->prepend = $string;
		}

		return $this;
	}

	public function append($string)
	{
		if (is_null($this->append)) {
			$this->append = $string;
		}

		return $this;
	}

	protected function initPlainInput()
	{
		if (empty($this->view)) {
			$this->view = 'py-mgr-page::tpl.form.input';
		}
	}

	protected function defaultAttribute($attribute, $value)
	{
		if (!array_key_exists($attribute, $this->attributes)) {
			$this->attribute($attribute, $value);
		}

		return $this;
	}
}
