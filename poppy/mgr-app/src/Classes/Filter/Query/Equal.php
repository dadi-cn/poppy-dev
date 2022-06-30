<?php

namespace Poppy\MgrApp\Classes\Filter\Query;

use Illuminate\Support\Carbon;
use Poppy\MgrApp\Classes\Filter\Traits\AsDatetime;
use Poppy\MgrApp\Classes\Filter\Traits\AsSelect;
use Poppy\MgrApp\Classes\Filter\Traits\AsText;
use Poppy\MgrApp\Classes\Form\Traits\UseOptions;
use Poppy\MgrApp\Classes\Form\Traits\UsePlaceholder;

class Equal extends FilterItem
{
    use AsSelect, AsText, AsDatetime,
        UsePlaceholder,
        UseOptions;

    protected string $query = 'where';

    /**
     * Get condition of this filter.
     *
     * @param array $inputs
     * @return array|null
     */
    public function condition(array $inputs): ?array
    {
        $this->defaultValue($inputs);
        if (!$this->value) {
            return null;
        }

        $value = $this->value;

        switch ($this->type) {
            case 'datetime':
                // 获取格式化属性中的类型
                $type = $this->attr['type'] ?? 'datetime';
                switch ($type) {
                    case 'date':
                        $start = Carbon::createFromFormat('Y-m-d', $value)->startOfDay()->toDateTimeString();
                        $end   = Carbon::createFromFormat('Y-m-d', $value)->endOfDay()->toDateTimeString();
                        break;
                    case 'month':
                        $start = Carbon::createFromFormat('Y-m', $value)->startOfMonth()->toDateTimeString();
                        $end   = Carbon::createFromFormat('Y-m', $value)->endOfMonth()->toDateTimeString();
                        break;
                    case 'datetime':
                    default:
                        $start = Carbon::parse($value)->toDateTimeString();
                        $end   = Carbon::parse($value)->toDateTimeString();
                        break;
                    case 'year':
                        $start = Carbon::createFromFormat('Y', $value)->startOfYear()->toDateTimeString();
                        $end   = Carbon::createFromFormat('Y', $value)->endOfYear()->toDateTimeString();
                        break;
                }
                $this->query = 'whereBetween';
                return $this->buildCondition($this->name, [$start, $end]);
            case 'text':
            default:
                return $this->buildCondition($this->name, $this->value);
        }

    }
}
