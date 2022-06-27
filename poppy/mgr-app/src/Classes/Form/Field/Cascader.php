<?php

namespace Poppy\MgrApp\Classes\Form\Field;

use Poppy\MgrApp\Classes\Form\FormItem;
use Poppy\MgrApp\Classes\Form\Traits\UseCascader;
use Poppy\MgrApp\Classes\Form\Traits\UseOptions;
use Poppy\MgrApp\Classes\Form\Traits\UsePlaceholder;

class Cascader extends FormItem
{
    use UseOptions, UsePlaceholder, UseCascader;
}
