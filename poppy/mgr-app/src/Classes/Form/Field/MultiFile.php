<?php

namespace Poppy\MgrApp\Classes\Form\Field;

use Illuminate\Support\Str;
use function collect;

class MultiFile extends File
{

    protected array $rules = [
        'array',
    ];


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
                $this->setAttribute('limit', (int) $max);
            }
        });
        return parent::attributes();
    }
}
