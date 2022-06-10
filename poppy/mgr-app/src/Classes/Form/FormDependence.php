<?php

namespace Poppy\MgrApp\Classes\Form;

use Illuminate\Support\Str;
use Poppy\Framework\Helper\StrHelper;
use Poppy\MgrApp\Classes\Form\Traits\UseFieldAttr;

/**
 * 表单依赖定义
 */
abstract class FormDependence
{

    use UseFieldAttr;

    /**
     * 字段名字
     * @var string
     */
    private string $name;

    /**
     * 定义的类型/使用的场景
     * @var string[]
     */
    protected array $type;

    /**
     * 字段参数
     * @var array
     */
    protected array $params;

    /**
     * 动态值参数
     * @var array
     */
    protected array $values;

    /**
     * 表单条目
     */
    public function __construct()
    {
        // element
        $this->name = StrHelper::slug(Str::afterLast(get_called_class(), '\\'));
        $this->fieldAttrInit();
    }

    public function name()
    {
        return $this->name;
    }


    protected function dropField($struct)
    {
        unset($struct['name'], $struct['label']);
        return $struct;
    }

    public function params($params)
    {
        $arrParams    = StrHelper::parseKey($params);
        $this->params = $arrParams;
    }

    public function values($values)
    {
        $this->values = $values;
    }

    /**
     * 返回字段定义
     * @return array
     */
    public function field(): array
    {
        return [];
    }

    /**
     * 返回属性定义
     * @return array
     */
    public function attr(): array
    {
        return [];
    }

}
