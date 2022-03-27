<?php

namespace Poppy\MgrPage\Classes\Grid\Displayer;


/**
 * Class Copyable.
 *
 * @see https://codepen.io/shaikmaqsood/pen/XmydxJ
 */
class Copyable extends AbstractDisplayer
{
    public function display()
    {
        return <<<HTML
<span data-text="{$this->getValue()}" class="J_copy" style="cursor: pointer;">
    <i class="fa fa-copy"></i> {$this->getValue()}
</span>&nbsp;
HTML;
    }
}
