<?php

namespace Poppy\MgrPage\Classes\Grid\Filter;

use Illuminate\Support\Arr;

class BetweenDate extends AbstractFilter
{
    /**
     * @inheritDoc
     */
    protected $view = 'py-mgr-page::tpl.filter.between_date';

    protected $variables = [
        'layui-range' => 'true',
        'layui-type'  => 'date',
    ];

    /**
     * Get condition of this filter.
     *
     * @param array $inputs
     *
     * @return mixed
     */
    public function condition(array $inputs)
    {
        if (!Arr::has($inputs, $this->column)) {
            return;
        }

        $this->value = Arr::get($inputs, $this->column);
        if (!$this->value) {
            return;
        }
        [$start, $end] = explode(' - ', $this->value);


        return $this->buildCondition([
            [$this->column, '<=', trim($end)],
            [$this->column, '>=', trim($start)],
        ]);
    }

    public function variables()
    {
        $variables = parent::variables();
        return array_merge($variables, ['variables' => $this->variables]);
    }

    public function withTime()
    {
        $this->variables['layui-type'] = 'datetime';
    }
}
