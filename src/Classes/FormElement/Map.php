<?php namespace Poppy\Framework\Classes\FormElement;

use Poppy\Framework\Classes\Contracts\FormElement as FormElementContract;

class Map extends FormTypeBase implements FormElementContract
{
	protected $formRelation;
	protected $posName;
	protected $zoom;
	protected $value;

	public function __construct($name, $setting, $value = null)
	{
		parent::__construct($name, $setting, $value);
		$this->formRelation = explode(',', $setting['form_relation']);
		$this->posName      = 'pos_name';
		$this->zoom         = 'zoom';
		$this->value        = $value;
	}

	/**
	 * 渲染HTML
	 * @return mixed
	 */
	public function render()
	{
		$pos_name  = $this->posName;
		$lng_name  = isset($this->formRelation['0']) ? $this->formRelation['0'] : '';
		$lat_name  = isset($this->formRelation['1']) ? $this->formRelation['1'] : '';
		$zoom_name = $this->zoom;
		$lng       = isset($this->value[$lng_name]) ? $this->value[$lng_name] : '';
		$lat       = isset($this->value[$lat_name]) ? $this->value[$lat_name] : '';

		return \Form::mapMarker($pos_name, $lng_name, $lat_name, $zoom_name, $lng, $lat, '');
	}
}