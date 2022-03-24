<?php

namespace Poppy\MgrApp\Classes\Grid;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;

class Row
{
    /**
     * 行号
     * @var int
     */
    public int $number;

    /**
     * 行数据
     * @var array
     */
    protected array $data;

    /**
     * 主键名称
     * @var string
     */
    protected string $pkName;

    /**
     * 创建 Row
     * @param int    $number  索引
     * @param array  $data    模型中查询出来的数据
     * @param string $pk_name 查询出来的主键
     */
    public function __construct(int $number, array $data, string $pk_name)
    {
        $this->data   = $data;
        $this->number = $number;
        $this->pkName = $pk_name;
    }

    /**
     * 获取主键的值
     * @return mixed
     */
    public function getKey()
    {
        return Arr::get($this->data, $this->pkName);
    }

    /**
     * 获取当前行的值
     * @return array
     */
    public function model(): array
    {
        return $this->data;
    }


    public function __get($attr)
    {
        return Arr::get($this->data, $attr, '');
    }

    /**
     * 设置或者获取当前列的值
     * @param string $name
     * @param mixed  $value
     * @return string|self
     */
    public function column(string $name, $value = null)
    {
        if (is_null($value)) {
            $column = Arr::get($this->data, $name);

            return $this->output($column);
        }

        if ($value instanceof Closure) {
            $value = $value->call($this, $this->column($name));
        }

        Arr::set($this->data, $name, $value);

        return $this;
    }

    /**
     * 输出列的值
     * @param mixed $value
     * @return ?string
     */
    protected function output($value): ?string
    {
        if ($value instanceof Renderable) {
            $value = $value->render();
        }

        if ($value instanceof Htmlable) {
            $value = $value->toHtml();
        }

        if ($value instanceof Jsonable) {
            $value = $value->toJson(JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if (!is_null($value) && !is_scalar($value)) {
            return sprintf('%s', var_export($value, true));
        }

        return $value;
    }
}
