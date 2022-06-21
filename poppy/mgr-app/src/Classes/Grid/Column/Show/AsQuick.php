<?php

namespace Poppy\MgrApp\Classes\Grid\Column\Show;

use Closure;
use Poppy\MgrApp\Classes\Grid\Column\Column;
use Poppy\MgrApp\Classes\Grid\Column\Option\EditOption;

trait AsQuick
{

    /**
     * 渲染为ID
     * @return $this
     */
    public function quickId($large = false): self
    {
        $width = $large ? 110 : 90;
        $this->width($width, true)->align('center');
        return $this;
    }

    /**
     * 渲染为标题, 默认显示 15个汉字, large 模式显示 20个汉字左右
     * @return $this
     */
    public function quickTitle($large = false): self
    {
        $width = $large ? 320 : 250;
        $this->ellipsis()->width($width, true)->copyable();
        return $this;
    }

    /**
     * 渲染为 Datetime 时间
     * @return $this
     */
    public function quickDatetime(): self
    {
        $this->width(170, true)->align('center');
        return $this;
    }

    /**
     * 定义快捷样式
     * @return $this
     */
    public function quickIcon($num = 3, $fixed = true): self
    {
        $width = 16 + $num * 44;
        $this->width($width, true)->align('center');
        if ($fixed) {
            $this->fixed();
        }
        return $this;
    }
}
