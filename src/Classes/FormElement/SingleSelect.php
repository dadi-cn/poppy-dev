<?php namespace Poppy\Framework\Classes\FormElement;

use Poppy\Framework\Classes\Contracts\FormElement as FormElementContract;
use Poppy\Framework\Helper\StrHelper;

class SingleSelect extends FormTypeBase implements FormElementContract
{
	const PC_TYPE_RADIO  = 'radio';
	const PC_TYPE_SELECT = 'select';

	protected static $pcTypeDesc = [
		self::PC_TYPE_RADIO  => '单选(Radio)',
		self::PC_TYPE_SELECT => '选择框(Select)',
	];
	protected        $pcType;
	protected        $singleOptions;

	public function __construct($name, $setting, $value = null)
	{
		parent::__construct($name, $setting, $value);
		$this->defaultValue  = isset($setting['single_select_default_value']) ? $setting['single_select_default_value'] : '';
		$this->pcType        = isset($setting['single_select_pc_type']) ? $setting['single_select_pc_type'] : self::PC_TYPE_RADIO;
		$this->placeholder   = isset($setting['single_select_placeholder']) ? $setting['single_select_placeholder'] : '';
		$this->singleOptions = isset($setting['single_select_options']) ? $setting['single_select_options'] : '';
		if (is_string($this->singleOptions)) {
			$this->singleOptions = StrHelper::parseKey($this->singleOptions);
		}
		if ($this->placeholder) {
			$this->form_options['placeholder'] = $this->placeholder;
		}
	}

	/**
	 * 渲染HTML
	 * @return mixed
	 */
	public function render()
	{
		$value = $this->value == null ? $this->defaultValue : $this->value;
		if ($this->pcType == self::PC_TYPE_RADIO) {
			return \Form::radios($this->name, $this->singleOptions, $value, $this->form_options);
		}
		elseif ($this->pcType == self::PC_TYPE_SELECT) {
			return \Form::select($this->name, $this->singleOptions, $value, $this->form_options);
		}

		return '';
	}

	public static function pcTypeLinear()
	{
		return self::$pcTypeDesc;
	}
}