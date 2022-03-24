<?php

namespace Poppy\MgrApp\Classes\Widgets;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\MgrApp\Classes\Contracts\Structable;
use Poppy\MgrApp\Classes\Form\FormItem;
use Poppy\MgrApp\Classes\Grid\Filter\FilterDef;
use Poppy\MgrApp\Classes\Grid\Filter\Query\Between;
use Poppy\MgrApp\Classes\Grid\Filter\Query\EndsWith;
use Poppy\MgrApp\Classes\Grid\Filter\Query\Equal;
use Poppy\MgrApp\Classes\Grid\Filter\Query\FilterItem;
use Poppy\MgrApp\Classes\Grid\Filter\Query\Gt;
use Poppy\MgrApp\Classes\Grid\Filter\Query\Gte;
use Poppy\MgrApp\Classes\Grid\Filter\Query\In;
use Poppy\MgrApp\Classes\Grid\Filter\Query\Like;
use Poppy\MgrApp\Classes\Grid\Filter\Query\Lt;
use Poppy\MgrApp\Classes\Grid\Filter\Query\Lte;
use Poppy\MgrApp\Classes\Grid\Filter\Query\NotEqual;
use Poppy\MgrApp\Classes\Grid\Filter\Query\NotIn;
use Poppy\MgrApp\Classes\Grid\Filter\Query\Scope;
use Poppy\MgrApp\Classes\Grid\Filter\Query\StartsWith;
use Poppy\MgrApp\Classes\Grid\Filter\Query\Where;
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
final class FilterWidget implements Structable
{

    /**
     * 表单内的表单条目集合
     * @var Collection
     */
    protected Collection $items;


    private int $actionWidth = 4;

    private bool $enableExport = false;

    /**
     * 全局范围
     * @var Collection
     */
    private Collection $scopes;


    public function __construct()
    {
        $this->items = collect();
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
     * @param string $method    类型
     * @param array  $arguments 传入的参数
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
     * 查询条件
     * @return array
     */
    public function conditions(): array
    {
        return array_merge(
            $this->filterConditions(),
            $this->scopeConditions()
        );
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
                'export' => $this->enableExport
            ],
            'items'  => $items->toArray(),
        ];
    }

    /**
     * 按钮的宽度(默认 4), 这里不会处理按钮的位置
     * @param int  $width  宽度
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
     * Get all conditions of the filters.
     *
     * @return array
     */
    public function filterConditions(): array
    {
        $inputs = Arr::dot(request()->all());

        $inputs = array_filter($inputs, function ($input) {
            return $input !== '' && !is_null($input);
        });

        if (empty($inputs)) {
            return [];
        }

        $params = [];

        foreach ($inputs as $key => $value) {
            Arr::set($params, $key, $value);
        }

        $conditions = [];
        foreach ($this->items as $filter) {
            /** @var FilterItem $filter */
            $conditions[] = $filter->condition($params);
        }

        return array_filter($conditions);
    }


    /**
     * 添加全局范围, 在添加全局范围之后, 如果不传入 Scope, 则默认为第一个 Scope
     * @param string $key
     * @param string $label
     *
     * @return mixed
     */
    public function scope(string $key, string $label)
    {
        return tap(new Scope($key, $label), function (Scope $scope) {
            return $this->scopes->push($scope);
        });
    }

    /**
     * Get all filter scopes.
     *
     * @return Collection
     */
    public function getScopes(): Collection
    {
        return $this->scopes;
    }

    /**
     * 范围结构
     * @return Collection
     */
    public function getScopesStruct(): Collection
    {
        return $this->scopes->map(function (Scope $scope) {
            return $scope->struct();
        });
    }

    /**
     * 获取当前的Scope,
     * 支持未传入
     * @return Scope|null
     */
    public function getCurrentScope(): ?Scope
    {
        $key = request(Scope::QUERY_NAME);
        if ($key) {
            return $this->scopes->first(function ($scope) use ($key) {
                return $scope->value == $key;
            });
        } else {
            return $this->scopes->first();
        }
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
