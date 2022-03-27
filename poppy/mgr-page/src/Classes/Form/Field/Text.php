<?php

namespace Poppy\MgrPage\Classes\Form\Field;

use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Poppy\MgrPage\Classes\Form\Field;
use Poppy\MgrPage\Classes\Form\Traits\PlainInput;

class Text extends Field
{
    use PlainInput;

    /**
     * @var string
     */
    protected $icon = 'fa-pencil';


    /**
     * @var string 默认类型(Number 可覆盖)
     */
    protected $type = 'text';

    /**
     * Set custom fa-icon.
     *
     * @param string $icon
     *
     * @return $this
     */
    public function icon(string $icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Render this filed.
     *
     * @return Factory|View
     */
    public function render()
    {
        $this->initPlainInput();

        $this->prepend('<i class="fa ' . $this->icon . ' fa-fw"></i>')
            ->defaultAttribute('id', $this->id)
            ->defaultAttribute('class', 'layui-input ' . $this->getElementClassString())
            ->defaultAttribute('placeholder', $this->getPlaceholder());

        $this->addVariables([
            'prepend' => $this->prepend,
            'append'  => $this->append,
            'type'    => $this->type,
        ]);

        return parent::render();
    }

    /**
     * Add datalist element to Text input.
     *
     * @param array $entries
     *
     * @return $this
     */
    public function datalist($entries = [])
    {
        $this->defaultAttribute('list', "list-{$this->id}");

        $datalist = "<datalist id=\"list-{$this->id}\">";
        foreach ($entries as $k => $v) {
            $datalist .= "<option value=\"{$k}\">{$v}</option>";
        }
        $datalist .= '</datalist>';

        return $this->append($datalist);
    }
}
