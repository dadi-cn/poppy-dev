<?php

namespace Poppy\MgrPage\Classes\Form\Field;

class Password extends Text
{
	public function render()
	{
		$this->prepend('<i class="fa fa-eye-slash fa-fw"></i>');

		$this->addVariables([
			'type' => 'password',
		]);
		return parent::render();
	}
}
