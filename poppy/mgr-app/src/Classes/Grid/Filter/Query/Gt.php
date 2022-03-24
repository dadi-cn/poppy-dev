<?php

namespace Poppy\MgrApp\Classes\Grid\Filter\Query;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Poppy\MgrApp\Classes\Form\Traits\UseOptions;
use Poppy\MgrApp\Classes\Form\Traits\UsePlaceholder;
use Poppy\MgrApp\Classes\Grid\Filter\Traits\AsDatetime;
use Poppy\MgrApp\Classes\Grid\Filter\Traits\AsSelect;
use Poppy\MgrApp\Classes\Grid\Filter\Traits\AsText;

class Gt extends FilterItem
{

    use AsSelect, AsText, AsDatetime,
        UsePlaceholder,
        UseOptions;

    /**
     * Get condition of this filter.
     *
     * @param array $inputs
     *
     * @return array|mixed|void
     */
    public function condition(array $inputs)
    {
        $value = Arr::get($inputs, $this->name);

        if (is_null($value)) {
            return;
        }

        $this->value = $value;


        switch ($this->type) {
            case 'datetime':
                // 获取格式化属性中的类型
                $type = $this->attr['type'] ?? 'datetime';
                $start = $this->getEndFrom($value, $type);
                return $this->buildCondition([
                    [$this->name, '>', trim($start)],
                ]);
            case 'text':
            default:
                return $this->buildCondition($this->name, '>', $this->value);
        }


    }

    public function struct(): array
    {
        $struct            = parent::struct();
        $struct['options'] = array_merge(
            $struct['options'] ?? [], [
                'prepend' => '大于'
            ]
        );
        return $struct;
    }
}
