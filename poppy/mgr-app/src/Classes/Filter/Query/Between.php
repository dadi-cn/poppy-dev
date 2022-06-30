<?php

namespace Poppy\MgrApp\Classes\Filter\Query;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Poppy\MgrApp\Classes\Filter\Traits\AsDatetime;
use Poppy\MgrApp\Classes\Filter\Traits\AsSelect;
use Poppy\MgrApp\Classes\Filter\Traits\AsText;
use Poppy\MgrApp\Classes\Form\Traits\UseOptions;
use Poppy\MgrApp\Classes\Form\Traits\UsePlaceholder;

/**
 * 介于 XX 之间, 默认是 文本 类型
 */
class Between extends FilterItem
{
    use AsSelect, AsText, AsDatetime,
        UsePlaceholder,
        UseOptions;

    public function __construct($column = '', string $label = '')
    {
        parent::__construct($column, $label);
        $this->type = 'text';
    }

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

        $value = array_filter((array) $this->value, function ($val) {
            return $val !== '';
        });

        if (empty($value)) {
            return null;
        }

        $start = $value['start'] ?? '';
        $end   = $value['end'] ?? '';
        switch ($this->type) {
            case 'datetime':
                // 获取格式化属性中的类型
                $type = $this->attr['type'] ?? 'datetime';
                switch ($type) {
                    case 'date':
                        $start = Carbon::createFromFormat('Y-m-d', $start)->startOfDay()->toDateTimeString();
                        $end   = Carbon::createFromFormat('Y-m-d', $end)->endOfDay()->toDateTimeString();
                        break;
                    case 'month':
                        $start = Carbon::createFromFormat('Y-m', $start)->startOfMonth()->toDateTimeString();
                        $end   = Carbon::createFromFormat('Y-m', $end)->endOfMonth()->toDateTimeString();
                        break;
                    case 'datetime':
                        $start = Carbon::parse($start)->toDateTimeString();
                        $end   = Carbon::parse($end)->toDateTimeString();
                        break;
                    default:
                        return null;
                }
                return $this->buildCondition([
                    [$this->name, '<=', trim($end)],
                    [$this->name, '>=', trim($start)],
                ]);
            case 'text':
            default:
                if (!$start) {
                    return $this->buildCondition($this->name, '<=', $start);
                }

                if (!$end) {
                    return $this->buildCondition($this->name, '>=', $end);
                }
                $this->query = 'whereBetween';
                return $this->buildCondition($this->name, $this->value);
        }

    }
}
