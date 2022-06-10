<?php

namespace Poppy\MgrApp\Classes\Form;

use Illuminate\Support\Str;

/**
 * 表单
 */
class FieldDef
{

    private static array $dependencies = [];

    /**
     * 创建表单条目
     * @param string $type 字段类型
     * @param string $name 表单字段Name
     * @param string $label 标签
     * @return FormItem|null
     */
    public static function create(string $type, string $name, string $label): ?FormItem
    {
        $class = __NAMESPACE__ . '\\Field\\' . Str::ucfirst($type);
        if (!class_exists($class)) {
            return null;
        }
        return new $class($name, $label);
    }

    /**
     * 注册依赖
     */
    public static function registerDependencies()
    {
        $dependencies = sys_hook('poppy.mgr-app.form-dependence');
        if (!is_array($dependencies)) {
            return;
        }
        foreach ($dependencies as $dependency) {
            if (!class_exists($dependency)) {
                sys_error('mgr-app', __CLASS__, "表单依赖类不存在: {$dependency}");
                continue;
            }
            $objDepend = new $dependency();
            if (!($objDepend instanceof FormDependence)) {
                sys_error('mgr-app', __CLASS__, "表单依赖类未继承: FormDependence");
                continue;
            }
            if (!isset(self::$dependencies[$dependency])) {
                self::$dependencies[$objDepend->name()] = $dependency;
            }
        }
    }

    /**
     * 获取关联数据
     * @param string $name
     * @param string $use
     * @param string $params
     * @return array
     */
    public static function fetchDepend(string $name, string $use, string $params = ''): array
    {

        $depend = self::$dependencies[$name] ?? '';
        if (!$depend) {
            return  [];
        }
        /** @var FormDependence $objDepend */
        $objDepend = new $depend();
        $objDepend->params($params);
        if ($use === 'field') {
            return $objDepend->field();
        }
        return $objDepend->attr();
    }
}
