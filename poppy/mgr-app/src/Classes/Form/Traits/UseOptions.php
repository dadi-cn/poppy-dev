<?php

namespace Poppy\MgrApp\Classes\Form\Traits;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

trait UseOptions
{

    /**
     * Set options.
     *
     * @param array|callable|string|Arrayable $options
     * @return $this
     */
    public function options($options = []): self
    {
        if ($this->getAttribute('depend')) {
            sys_error('mgr-app', __CLASS__, '已存在依赖, 不能设置选项, 依赖字段和选项不能共存');
            return $this;
        }
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        $first = Arr::first($options);
        $group = false;
        if (is_array($first) && count($first['options'] ?? [])) {
            // group use at select(分组)
            $group = true;
        }

        $formatOptions = [];
        if (is_scalar($first)) {
            foreach ($options as $key => $option) {
                $formatOptions[] = [
                    'value' => $key,
                    'label' => $option
                ];
            }
            $options = $formatOptions;
        }

        $this->setAttribute('options', $options);
        $this->setAttribute('group', $group);
        return $this;
    }

    /**
     * 是否选项可搜索
     * @return $this
     */
    public function filterable(): self
    {
        $this->setAttribute('filterable', true);
        return $this;
    }
}
