<?php

namespace Poppy\MgrPage\Classes\Form\_Back;

class Fieldset
{
    protected $name = '';

    public function __construct()
    {
        $this->name = uniqid('fieldset-');
    }

    public function start($title)
    {


        return <<<HTML
<div>
    <div style="height: 20px; border-bottom: 1px solid #eee; text-align: center;margin-top: 20px;margin-bottom: 20px;">
      <span style="font-size: 16px; background-color: #ffffff; padding: 0 10px;">
        <a data-toggle="collapse" href="#{$this->name}" class="{$this->name}-title">
          <i class="fa fa-angle-double-up"></i>&nbsp;&nbsp;{$title}
        </a>
      </span>
    </div>
    <div class="collapse in" id="{$this->name}">
HTML;
    }

    public function end()
    {
        return '</div></div>';
    }

    public function collapsed()
    {
        return $this;
    }
}
