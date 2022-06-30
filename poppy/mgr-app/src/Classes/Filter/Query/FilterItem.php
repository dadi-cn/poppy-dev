<?php

namespace Poppy\MgrApp\Classes\Filter\Query;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Poppy\Framework\Helper\StrHelper;
use Poppy\MgrApp\Classes\Contracts\Structable;

/**
 * 过滤条目
 * @property-read  $name   当前列的名称
 * @property-read  $value  输入值
 */
abstract class FilterItem implements Structable
{

    protected int $width = 4;

    /**
     * @var string
     */
    protected $name = '';


    /**
     * 默认
     * @var string
     */
    protected string $type = '';

    /**
     * Label of presenter.
     *
     * @var string
     */
    protected $label;

    /**
     * @var array|string
     */
    protected $value;

    /**
     * 默认值
     * @var array|string|null
     */
    protected $defaultValue = null;


    /**
     * Query for filter.
     * @var string
     */
    protected string $query = 'where';


    /**
     * 属性
     * @var array
     */
    protected array $attr = [];

    /**
     * @var FilterItem
     */
    protected $parent;

    /**
     * AbstractFilter constructor.
     *
     * @param string|Closure $column
     * @param string $label
     */
    public function __construct($column = '', string $label = '')
    {
        $this->name  = $column;
        $this->label = $this->formatLabel($label);
    }

    /**
     * 设置列宽度
     * @return $this
     */
    public function width($width): self
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @param FilterItem $filter
     */
    public function setParent(FilterItem $filter)
    {
        $this->parent = $filter;
    }

    /**
     * 获取查询条件
     * @param array $inputs
     *
     * @return array|mixed|null
     */
    public function condition(array $inputs)
    {
        $value = Arr::get($inputs, $this->name);

        if (is_null($value)) {
            $this->value = $this->defaultValue;
        }
        else {
            $this->value = $value;
        }

        return $this->buildCondition($this->name, $this->value);
    }


    public function struct(): array
    {
        $query = Str::snake(Str::afterLast(get_called_class(), '\\'));
        return array_merge([
            'name'    => StrHelper::formatId($this->name),
            'label'   => $this->label,
            'width'   => $this->width,
            'value'   => $this->value ?: $this->defaultValue,
            'query'   => $query,
            'type'    => $this->type,
            'options' => $this->attr ?: [],
        ]);
    }

    public function __get($attr)
    {
        if (in_array($attr, ['column', 'value'])) {
            return $this->{$attr};
        }
        return null;
    }


    /**
     * Set default value for filter.
     *
     * @param null $default
     * @return $this
     */
    public function default($default = null): self
    {
        if ($default) {
            $this->defaultValue = $default;
        }

        return $this;
    }

    /**
     * Get value of current filter.
     *
     * @return array|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * 默认值
     * @param array $inputs
     * @return void
     */
    protected function defaultValue(array $inputs)
    {
        $value = Arr::get($inputs, $this->name);
        if (is_null($value) && !is_null($this->defaultValue)) {
            $this->value = $this->defaultValue;
        }
        else {
            $this->value = $value;
        }
    }

    /**
     * @param $inputs
     * @return array
     */
    protected function sanitizeInputs(&$inputs): array
    {
        if (!$this->name) {
            return $inputs;
        }

        $inputs = collect($inputs)->filter(function ($input, $key) {
            return Str::startsWith($key, "{$this->name}_");
        })->mapWithKeys(function ($val, $key) {
            $key = str_replace("{$this->name}_", '', $key);
            return [$key => $val];
        })->toArray();
        return $inputs;
    }


    /**
     * 格式化 Label
     * @param string $label
     * @return string
     */
    protected function formatLabel(string $label): string
    {
        $label = $label ?: ucfirst($this->name);
        return str_replace(['.', '_'], ' ', $label);
    }


    /**
     * Build conditions of filter.
     *
     * @return mixed
     */
    protected function buildCondition(): array
    {
        $column = explode('.', $this->name);

        if (count($column) == 1) {
            // where ['title', 'like', '%我%']
            return [$this->query => func_get_args()];
        }

        return $this->buildRelationQuery(...func_get_args());
    }

    /**
     * Build query condition of model relation.
     *
     * @return array
     */
    protected function buildRelationQuery(): array
    {
        $args = func_get_args();

        [$relation, $args[0]] = explode('.', $this->name);

        return [
            'whereHas' => [
                $relation, function ($relation) use ($args) {
                    call_user_func_array([$relation, $this->query], $args);
                },
            ],
        ];
    }

    /**
     * 字段属性
     * @param string|array $attr
     * @param mixed $value
     * @return $this
     */
    protected function setAttribute($attr, $value = ''): self
    {
        if (is_array($attr)) {
            foreach ($attr as $att => $val) {
                $this->attr[$att] = $val;
            }
        }
        else {
            $this->attr[$attr] = $value;
        }
        return $this;
    }
}
