<?php

namespace Poppy\MgrApp\Classes\Form\Field;

use Illuminate\Support\Str;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Form\FormItem;
use Poppy\MgrApp\Exceptions\InvalidFieldParamException;
use function collect;

class Number extends FormItem
{
    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->setAttribute('step', 1);
        $this->rules = [
            Rule::numeric(),
        ];
    }

    /**
     * 数值精度
     * @param int $precision
     * @return $this
     * @throws InvalidFieldParamException
     */
    public function precision(int $precision): self
    {
        if ($length = Str::after($this->getAttribute('step'), '.')) {
            if ($precision > strlen($length)) {
                throw new InvalidFieldParamException('precision at field number not correct');
            }
        }
        $this->setAttribute('precision', $precision);
        return $this;
    }

    /**
     * @param int|float $step 计数器步长
     * @param bool $strictly 是否只能输入 step 的倍数
     * @return $this
     */
    public function step($step, bool $strictly = false): self
    {
        $this->setAttribute('step', $step);
        $this->setAttribute('step-strictly', $strictly);
        return $this;
    }

    protected function attributes(): object
    {
        // 解析 max 规则, 匹配属性
        collect($this->rules)->each(function ($rule) {
            if (Str::startsWith($rule, 'max')) {
                $max = Str::after($rule, 'max:');
                $this->setAttribute('max', (float) $max);
            }
            if (Str::startsWith($rule, 'min')) {
                $min = Str::after($rule, 'min:');
                $this->setAttribute('min', (float) $min);
            }
        });
        return parent::attributes();
    }
}
