<?php

namespace Poppy\Area\Classes\Form\Field;

use Poppy\Area\Models\SysArea;
use Poppy\MgrPage\Classes\Form\Field;

final class Area extends Field
{

    /**
     * @inheritDoc
     */
    protected $view = 'py-area::tpl.form.area';

    public function render()
    {
        $this->addVariables([
            'area' => SysArea::cityTree(),
        ]);
        return parent::render();
    }
}
