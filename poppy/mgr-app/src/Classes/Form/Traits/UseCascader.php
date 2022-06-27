<?php


namespace Poppy\MgrApp\Classes\Form\Traits;

trait UseCascader
{

    /**
     * @param int $width 占位符
     * @return self
     */
    public function width(int $width): self
    {
        $this->setAttribute('width', $width);
        return $this;
    }

    /**
     * 把节点的数据传递下去
     * @param string $url
     * @return self
     */
    public function lazy(string $url): self
    {
        $this->setAttribute('lazy', (bool) $url);
        $this->setAttribute('lazy_url', $url);
        return $this;
    }

    /**
     * 可以多选
     * @return self
     */
    public function multi(): self
    {
        $this->setAttribute('multi', true);
        return $this;
    }


    /**
     * 可以选择任意节点
     * @return self
     */
    public function checkStrictly(): self
    {
        $this->setAttribute('checkStrictly', true);
        return $this;
    }

}
