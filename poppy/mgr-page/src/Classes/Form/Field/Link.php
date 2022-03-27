<?php

namespace Poppy\MgrPage\Classes\Form\Field;

use Poppy\MgrPage\Classes\Form\Field;

class Link extends Field
{

    /**
     * @var string
     */
    protected $class = 'layui-btn-primary';

    /**
     * @var mixed|string
     */
    protected $url = '#';


    public function __construct($label = '')
    {
        $this->label = $label;
    }


    public function info()
    {
        $this->class = str_replace('layui-btn-primary', ' layui-btn-info ', $this->class);

        return $this;
    }


    public function warn()
    {
        $this->class = str_replace('layui-btn-primary', ' layui-btn-info ', $this->class);

        return $this;
    }

    public function iframe($width = 500, $height = 500)
    {
        $this->class .= ' J_iframe';
        $this->attribute([
            'data-width'  => $width,
            'data-height' => $height,
        ]);
        return $this;
    }

    public function small()
    {
        $this->class .= ' layui-btn-sm';
        return $this;
    }

    public function url($url)
    {
        $this->url = $url;
        return $this;
    }

    public function render()
    {
        $this->addVariables([
            'title' => $this->label,
            'url'   => $this->url,
        ]);
        $this->attribute([
            'class' => 'layui-btn ' . $this->class,
        ]);
        return parent::render();
    }
}
