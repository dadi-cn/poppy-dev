<?php namespace Poppy\Framework\Classes\FormElement;

class FormTypeBase
{
	protected $defaultValue;
	protected $placeholder;
	protected $value;
	protected $name;
	protected $form_options  = [];
	protected $label_options = [];
	protected $setting;
	protected $validator;

	public function __construct($name, $setting = [], $value = null)
	{
		$this->value   = $value;
		$this->name    = $name;
		$this->setting = $setting;
		if (isset($this->setting['form_options'])) {
			$this->form_options = array_merge($this->form_options, (array) $this->setting['form_options']);
		}
		$this->form_options['id'] = $name;

		if (isset($this->setting['label_options'])) {
			$this->label_options = array_merge($this->label_options, (array) $this->setting['label_options']);
		}
		$this->validator = $this->parseValidator();
		$this->initLabelOption();
		$this->initFormOption();
	}

	public function value()
	{
		return $this->value;
	}

	/**
	 * 获取默认值
	 * @return mixed
	 */
	public function defaultValue()
	{
		return $this->defaultValue;
	}

	/**
	 * 解析验证器
	 */
	public function parseValidator()
	{
		$validator = [];
		if (isset($this->setting['validator'])) {
			$rules = explode('|', $this->setting['validator']);
			foreach ($rules as $rule) {
				switch ($rule) {
					case 'required':
						$validator['required'] = true;
						break;
					case strpos($rule, 'digits_between') !== false:
						list($min, $max) = explode(',', str_replace('digits_between:', '', $rule));
						$validator['digits_between'] = [
							'min' => intval($min),
							'max' => intval($max),
						];
						break;
					case 'integer':
						$validator['integer'] = true;
						break;
					default:
						break;
				}
			}
		}

		return $validator;
	}

	public function initLabelOption()
	{
		if (!isset($this->label_options['class'])) {
			$this->label_options['class'] = '';
		}
		if (isset($this->validator['required']) && $this->validator['required']) {
			$this->label_options['class'] .= ' validation';
		}
		else {
			$this->label_options['class'] .= ' place';
		}
	}

	public function initFormOption()
	{
		if (isset($this->validator['required']) && $this->validator['required']) {
			$this->form_options['required'] = 'required';
		}
		if (isset($this->validator['digits_between']) && $this->validator['digits_between']) {
			$digitsBetween                = $this->validator['digits_between'];
			$this->form_options['number'] = 'true';
			$this->form_options['min']    = $digitsBetween['min'];
			$this->form_options['max']    = $digitsBetween['max'];
		}
		if (isset($this->validator['integer']) && $this->validator['integer']) {
			$this->form_options['number'] = 'true';
		}
	}

	public function label()
	{
		return \Form::label($this->form_options['id'], $this->setting['title'], $this->label_options);
	}

	public function tip()
	{
		if (isset($this->setting['desc'])) {
			return \Form::tip($this->setting['desc']);
		}

		return '';
	}
}