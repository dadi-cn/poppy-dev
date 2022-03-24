<?php

namespace Poppy\MgrApp\Classes\Form\Field;

use Poppy\MgrApp\Classes\Form\FormItem;
use Poppy\MgrApp\Classes\Form\Traits\UsePlaceholder;
use Poppy\MgrApp\Classes\Form\Traits\UseText;

/**
 * 文本输入框
 * @property string $prepend
 */
class Text extends FormItem
{

    use UseText, UsePlaceholder;

    /**
     * 默认数据
     * @var mixed
     */
    protected $default = '';

    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
    }

    public function clearable(): self
    {
        $this->setAttribute('clearable', true);
        return $this;
    }

    public function prefixIcon($icon): self
    {
        $this->setAttribute('prefix-icon', $icon);
        return $this;
    }

    public function suffixIcon($icon): self
    {
        $this->setAttribute('suffix-icon', $icon);
        return $this;
    }

    /**
     * 等宽字体显示
     * @return $this
     */
    public function monospace(): self
    {
        $this->setAttribute('monospace', true);
        return $this;
    }
}
