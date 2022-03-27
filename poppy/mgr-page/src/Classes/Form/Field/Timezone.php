<?php

namespace Poppy\MgrPage\Classes\Form\Field;

use DateTimeZone;
use function collect;

class Timezone extends Select
{
    protected $view = 'py-mgr-page::tpl.form.select';

    public function render()
    {
        $this->options = collect(DateTimeZone::listIdentifiers())->mapWithKeys(function ($timezone) {
            return [$timezone => $timezone];
        })->toArray();

        return parent::render();
    }
}
