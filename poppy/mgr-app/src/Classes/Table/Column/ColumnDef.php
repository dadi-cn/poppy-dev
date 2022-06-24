<?php

namespace Poppy\MgrApp\Classes\Table\Column;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrApp\Classes\Filter\Query\FilterItem;

/**
 * todo 未启用
 */
class ColumnDef
{
    /**
     * @var array
     */
    protected static array $supports = [];

    /**
     * 创建表单条目
     * @param string $type  字段类型
     * @param string $name  表单字段Name
     * @param string $label 标签
     * @return FilterItem|null
     * @throws ApplicationException
     */
    public static function create(string $type, string $name, string $label): ?FilterItem
    {
        $class = __NAMESPACE__ . '\\Render\\' . Str::ucfirst($type);
        if (!class_exists($class)) {
            return self::resolveQuery($type, [$name, $label]);
        }
        return new $class($name, $label);
    }


    /**
     * 扩展定义
     * @param string $name
     * @param string $filterClass
     */
    public static function extend(string $name, string $filterClass)
    {
        if (!is_subclass_of($filterClass, FilterItem::class)) {
            throw new InvalidArgumentException("The class [$filterClass] must be a type of " . FilterItem::class . '.');
        }
        static::$supports[$name] = $filterClass;
    }


    /**
     * @param string $type
     * @param array  $arguments
     *
     * @return FilterItem
     * @throws ApplicationException
     */
    public static function resolveQuery(string $type, array $arguments): FilterItem
    {
        if (!isset(static::$supports[$type])) {
            throw new ApplicationException('Abstract Class `' . $type . '` Not Exists');
        }
        return new static::$supports[$type](...$arguments);
    }
}
