<?php namespace Poppy\Framework\Classes\FormElement;

use Poppy\Framework\Classes\Contracts\FormElement as FormElementContract;

class File extends FormTypeBase implements FormElementContract
{
	private $viewPrefix = '';
	private $viewFile   = '';

	public function __construct($name, $setting, $value = null)
	{
		parent::__construct($name, $setting, $value);

		$this->viewPrefix = isset($setting['view_prefix']) ? $setting['view_prefix'] : '';
		$this->viewFile   = isset($setting['file_name']) ? $setting['file_name'] : '';
	}

	/**
	 * 渲染HTML
	 * @return mixed
	 */
	public function render()
	{
		return view($this->viewPrefix . $this->viewFile, [
			'prefix' => $this->name,
			'title'  => $this->setting['title'],
			'value'  => json_decode($this->value, true),
		])->render();
	}
}