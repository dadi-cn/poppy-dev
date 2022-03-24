<?php

namespace Poppy\MgrApp\Classes\Grid\Filter\Query;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Poppy\MgrApp\Classes\Contracts\Structable;

/**
 * @property-read string $value 值
 * @property-read string $label 标签
 */
class Scope implements Structable
{
    const QUERY_NAME = '_scope';

    /**
     * @var string
     */
    protected string $value = '';

    /**
     * @var string
     */
    protected string $label = '';

    /**
     * @var Collection
     */
    protected Collection $queries;

    /**
     * Scope constructor.
     *
     * @param string|int $value
     * @param string     $label
     */
    public function __construct($value, string $label)
    {
        $this->value   = (string) $value;
        $this->label   = $label ?: Str::studly($value);
        $this->queries = new Collection();
    }

    public function __get($attr)
    {
        if (in_array($attr, ['label', 'value'])) {
            return $this->{$attr};
        }
        return null;
    }

    /**
     * 获取模型查询条件
     * @return array
     */
    public function condition(): array
    {
        return $this->queries->map(function ($query) {
            return [$query['method'] => $query['arguments']];
        })->toArray();
    }

    /**
     * 将模型查询条件存储
     * @param string $method
     * @param array  $arguments
     *
     * @return $this
     */
    public function __call(string $method, array $arguments): self
    {
        $this->queries->push(compact('method', 'arguments'));
        return $this;
    }

    public function struct(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label,
        ];
    }
}
