<?php


namespace Poppy\MgrApp\Classes\Form\Traits;


trait UseDependence
{

    /**
     * 使用网络数据加载配置项
     * @param string $name 名称
     * @param string $params 参数
     * @return $this
     */
    public function depend(string $name, string $params = ''): self
    {
        $this->setAttribute('depend', $name);
        if ($params) {
            $this->setAttribute('depend-params', $params);
        }
        return $this;
    }
}
