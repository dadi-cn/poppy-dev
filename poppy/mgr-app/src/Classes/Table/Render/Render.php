<?php

namespace Poppy\MgrApp\Classes\Table\Render;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Renderable;

abstract class Render implements Renderable
{
    /**
     * @var object | array
     */
    protected $row;


    /**
     * @var mixed
     */
    protected $value;

    /**
     * 字段名称
     * @var string
     */
    protected string $field;

    /**
     * 创建一个渲染实例
     * @param mixed $value
     * @param object|array $row
     */
    public function __construct($value, $row, $field)
    {
        $this->value = $value;
        $this->row   = $row;
        $this->field = $field;
    }

    /**
     * 当前行的数据
     * @return array|object
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * 渲染方法
     * @return Jsonable
     */
    abstract public function render(): Jsonable;
}
