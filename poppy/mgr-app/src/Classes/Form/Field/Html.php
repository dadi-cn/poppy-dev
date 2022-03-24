<?php

namespace Poppy\MgrApp\Classes\Form\Field;

use Illuminate\Support\Arr;
use Poppy\MgrApp\Classes\Form\FormItem;

class Html extends FormItem
{
    /**
     * Htmlable.
     *
     * @var string|\Closure
     */
    protected $html = '';

    /**
     * @var string
     */
    protected $label = '';

    /**
     * @var bool
     */
    protected $plain = false;

    /**
     * Create a new Html instance.
     *
     * @param mixed $html
     * @param array $arguments
     */
    public function __construct($html, $arguments)
    {
        $this->html = $html;

        $this->label = Arr::get($arguments, 0);
    }

    /**
     * @return $this
     */
    public function plain()
    {
        $this->plain = true;

        return $this;
    }

    /**
     * Render html field.
     *
     * @return string
     */
    public function render()
    {
        if ($this->html instanceof \Closure) {
            $this->html = $this->html->call($this->form->model(), $this->form);
        }

        if ($this->plain) {
            return $this->html;
        }

        $viewClass = $this->getViewElementClasses();

        return <<<EOT
<div class="{$viewClass['form-group']}">
	<div class="{$viewClass['label']}">
		<label class="layui-form-auto-label {$viewClass['label_element']}">
			{$this->label}
		</label>
	</div>

	<div class="{$viewClass['field']}">
		<div class="layui-form-auto-field pt5">
			{$this->html}
		</div>
	</div>
</div>
EOT;
    }
}
