<?php

namespace Poppy\MgrApp\Classes\Form\Field;

use Poppy\MgrApp\Classes\Form\FormItem;
use Poppy\MgrApp\Classes\Form\Traits\UseDependence;
use Poppy\MgrApp\Classes\Form\Traits\UseOptions;
use Poppy\MgrApp\Classes\Form\Traits\UsePlaceholder;

class Select extends FormItem
{
    use UseOptions, UsePlaceholder, UseDependence;

    public function struct(): array
    {
        if ($this->getAttribute('depend') && $this->getAttribute('options')) {
            sys_error('mgr-app', __CLASS__, '已存在依赖, 不能设置选项, 依赖字段和选项不能共存');
            return [];
        }
        return parent::struct();
    }
}
