<?php

namespace Poppy\MgrApp\Classes\Dashboard;

use Poppy\Core\Classes\Traits\CoreTrait;
use Poppy\MgrApp\Classes\Traits\UseQuery;

/**
 * 用于同一个 Scope 下 / 无 Scope 下的面型表单配置
 */
abstract class PanelBase
{

    use CoreTrait, UseQuery;


    /**
     * 标题
     * @var string
     */
    protected string $title;

    /**
     * 布局宽度
     * @var int
     */
    protected int $width;

    /**
     * KEY
     * @var string
     */
    protected string $key;

    public function __construct($title, $width)
    {
        $this->title = $title;
        $this->width = $width;
    }

    /**
     * 获取识别KEY
     * @return string
     */
    public function key(): string
    {
        return $this->key;
    }


    abstract public function struct($query = ''): array;
    abstract public function resp(array $data = []);
}
