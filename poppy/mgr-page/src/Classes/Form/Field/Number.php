<?php

namespace Poppy\MgrPage\Classes\Form\Field;

use Poppy\Framework\Validation\Rule;

class Number extends Text
{

    protected $type = 'number';


    public function __construct($column = '', $arguments = [])
    {
        parent::__construct($column, $arguments);
        $this->rules[] = Rule::numeric();
    }


    public function render()
    {
        $this->default($this->default);

        $this->prepend('')->defaultAttribute('style', 'width: 100px');

        return parent::render();
    }

    /**
     * Set min value of number field.
     *
     * @param int $value
     * @return $this
     * @deprecated 使用 服务端验证替代 客户端(Form)
     *
     */
    public function min($value)
    {
        $this->attribute('min', $value);

        return $this;
    }

    /**
     * Set max value of number field.
     *
     * @param int $value
     * @return $this
     * @deprecated
     */
    public function max($value)
    {
        $this->attribute('max', $value);

        return $this;
    }
}
