<?php

namespace Poppy\MgrApp\Classes\Filter;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrApp\Classes\Contracts\Structable;
use Poppy\MgrApp\Classes\Filter\Query\Between;
use Poppy\MgrApp\Classes\Filter\Query\EndsWith;
use Poppy\MgrApp\Classes\Filter\Query\Equal;
use Poppy\MgrApp\Classes\Filter\Query\FilterItem;
use Poppy\MgrApp\Classes\Filter\Query\Gt;
use Poppy\MgrApp\Classes\Filter\Query\Gte;
use Poppy\MgrApp\Classes\Filter\Query\In;
use Poppy\MgrApp\Classes\Filter\Query\Like;
use Poppy\MgrApp\Classes\Filter\Query\Lt;
use Poppy\MgrApp\Classes\Filter\Query\Lte;
use Poppy\MgrApp\Classes\Filter\Query\NotEqual;
use Poppy\MgrApp\Classes\Filter\Query\NotIn;
use Poppy\MgrApp\Classes\Filter\Query\Scope;
use Poppy\MgrApp\Classes\Filter\Query\StartsWith;
use Poppy\MgrApp\Classes\Filter\Query\Where;
use Poppy\MgrApp\Classes\Form\FormItem;
use Poppy\MgrApp\Classes\Traits\UseScopes;
use ReflectionException;

/**
 * @method Where where(Closure $query, $label = '', $column = null) 自定义查询条件
 * @method Equal equal($column, $label = '') 相等
 * @method NotEqual notEqual($column, $label = '') 不等
 * @method Like like($column, $label = '') 匹配搜索
 * @method StartsWith startsWith($column, $label = '') 前半部分匹配
 * @method EndsWith endsWith($column, $label = '') 后半部分匹配
 * @method Gt gt($column, $label = '') 大于
 * @method Gte gte($column, $label = '') 大于等于
 * @method Lt lt($column, $label = '') 小于
 * @method Lte lte($column, $label = '') 小于
 * @method In in($column, $label = '') 包含
 * @method NotIn notIn($column, $label = '') 不包含
 * @method Between between($column, $label = '') 介于...
 */
class FilterPlugin implements Structable
{

    use UseScopes;

    /**
     * 表单内的表单条目集合
     * @var Collection
     */
    protected Collection $items;


    private int $actionWidth = 4;

    private bool $enableExport = false;


    public function __construct()
    {
        $this->items  = collect();
        $this->scopes = new Collection();
    }

    /**
     * 添加搜索条件
     * @param FilterItem $item
     * @return $this
     */
    public function addItem(FilterItem $item): self
    {
        $this->items->push($item);
        return $this;
    }

    /**
     * 获取表单的所有字段
     * @return FormItem[]|Collection
     */
    public function items(): Collection
    {
        return $this->items;
    }

    /**
     * Generate items and append to filter list
     * @param string $method 类型
     * @param array $arguments 传入的参数
     *
     * @return FormItem|$this
     * @throws ApplicationException
     * @throws ReflectionException
     */
    public function __call(string $method, array $arguments = [])
    {
        if ($method === 'where') {
            $filter = new Where(...$arguments);
            return tap($filter, function ($field) {
                $this->addItem($field);
            });
        }
        $name   = (string) Arr::get($arguments, 0);
        $label  = (string) Arr::get($arguments, 1);
        $filter = FilterDef::create($method, $name, $label);
        if (is_null($filter)) {
            throw new ApplicationException("Filter `${method}` not exists");
        }
        return tap($filter, function ($field) {
            $this->addItem($field);
        });
    }


    /**
     * 返回结构
     * 规则解析参考 : https://github.com/yiminghe/async-validator
     */
    public function struct(): array
    {
        $items = new Collection();
        $this->items->each(function (FilterItem $item) use ($items) {
            $struct = $item->struct();
            $items->push($struct);
        });
        return [
            'action' => [
                'width'  => $this->actionWidth,
                'export' => $this->enableExport,
            ],
            'items'  => $items->toArray(),
        ];
    }

    /**
     * 按钮的宽度(默认 4), 这里不会处理按钮的位置
     * @param int $width 宽度
     * @param bool $export 是否允许导出
     * @return void
     */
    public function action(int $width = 4, bool $export = false)
    {
        $this->actionWidth  = $width;
        $this->enableExport = $export;
    }


    /**
     * 是否启用了导出
     * @return bool
     */
    public function getEnableExport(): bool
    {
        return $this->enableExport;
    }


    /**
     * 根据不同的类型返回不同的查询条件
     * @param string $type
     * @return array
     */
    public function prepare(string $type = 'params'): array
    {
        if ($type === 'condition') {
            return array_merge(
                $this->filterConditions(),
                $this->scopeConditions()
            );
        }
        else {
            return array_merge(
                $this->filterParams(),
                $this->scopeParams()
            );
        }
    }


    /**
     * Get all conditions of the filters.
     *
     * @return array
     */
    private function filterParams(): array
    {
        $inputs = Arr::dot(request()->all());
        $inputs = collect($inputs)->except([
            '_query', 'page', 'pagesize', 'timestamp', 'sign',
        ]);

        return $inputs->toArray();
    }

    /**
     * Get all conditions of the filters.
     *
     * @return array
     */
    private function filterConditions(): array
    {
        $params = $this->filterParams();

        $conditions = [];
        foreach ($this->items as $filter) {
            /** @var FilterItem $filter */
            $conditions[] = $filter->condition($params);
        }

        return array_filter($conditions);
    }

    /**
     * Get scope conditions.
     * @return array
     */
    private function scopeParams(): array
    {
        if ($scope = $this->getCurrentScope()) {
            return [
                Scope::QUERY_NAME => $scope->value,
            ];
        }
        return [];
    }

    /**
     * Get scope conditions.
     * @return array
     */
    private function scopeConditions(): array
    {
        if ($scope = $this->getCurrentScope()) {
            return $scope->condition();
        }
        return [];
    }
}
