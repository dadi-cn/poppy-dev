<?php


namespace Poppy\MgrApp\Classes\Form\Traits;

use Illuminate\Support\Str;
use function collect;

trait UseText
{
    /**
     * 显示输入字符限制
     * @return $this
     */
    public function showWordLimit(): self
    {
        $this->setAttribute('show-word-limit', true);
        return $this;
    }


    protected function attributes(): object
    {
        // 解析 max 规则, 匹配属性
        collect($this->rules)->each(function ($rule) {
            if (Str::startsWith($rule, 'max')) {
                $max = Str::after($rule, 'max:');
                $this->setAttribute('maxlength', $max);
            }
        });
        return parent::attributes();
    }
}
