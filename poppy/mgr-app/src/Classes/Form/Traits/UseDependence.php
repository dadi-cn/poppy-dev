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
        if ($this->getAttribute('options')) {
            sys_error('mgr-app', __CLASS__, '已存在选项, 不得设置依赖, 依赖字段和选项不能共存');
            return $this;
        }
        $this->setAttribute('depend', $name);
        if ($params) {
            $this->setAttribute('depend-params', $params);
        }
        return $this;
    }
}
