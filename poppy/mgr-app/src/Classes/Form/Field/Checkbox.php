<?php

namespace Poppy\MgrApp\Classes\Form\Field;

use Illuminate\Support\Str;
use function collect;

class Checkbox extends Radio
{

    public function default($value): self
    {
        $value = is_array($value) ?: [$value];
        return parent::default($value);
    }

    /**
     * 匹配 max 规则
     * @return object
     */
    protected function attributes(): object
    {
        // 解析 max 规则, 匹配属性
        collect($this->rules)->each(function ($rule) {
            if (Str::startsWith($rule, 'max')) {
                $max = Str::after($rule, 'max:');
                $this->setAttribute('max', (int) $max);
            }
            if (Str::startsWith($rule, 'min')) {
                $min = Str::after($rule, 'min:');
                $this->setAttribute('min', (int) $min);
            }
        });
        return parent::attributes();
    }

    /**
     * @return $this
     */
    public function checkAll(): self
    {
        $this->setAttribute('check-all', true);
        return $this;
    }
}
