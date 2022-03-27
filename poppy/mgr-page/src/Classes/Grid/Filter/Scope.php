<?php

namespace Poppy\MgrPage\Classes\Grid\Filter;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use function input;
use function request;

class Scope implements Renderable
{
    const QUERY_NAME = '_scope';

    /**
     * @var string
     */
    public $key = '';

    /**
     * @var string
     */
    protected $label = '';

    /**
     * @var Collection
     */
    protected $queries;

    /**
     * Scope constructor.
     *
     * @param string|int $key
     * @param string     $label
     */
    public function __construct($key, $label = '')
    {
        $this->key   = (string) $key;
        $this->label = $label ?: Str::studly($key);

        $this->queries = new Collection();
    }

    /**
     * Get label.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Get model query conditions.
     *
     * @return array
     */
    public function condition(): array
    {
        return $this->queries->map(function ($query) {
            return [$query['method'] => $query['arguments']];
        })->toArray();
    }

    /**
     * Scope 因为涉及到刷新, 所以使用跳转的方式
     * 这种方式和 layui 的监听tab 不同, 这种会在刷新页面, tab 不会保留刷新的参数
     * @return string
     */
    public function render(): string
    {
        $url       = request()->fullUrlWithQuery([static::QUERY_NAME => $this->key]);
        $className = (string) input(static::QUERY_NAME) === $this->key ? 'class="layui-this"' : '';
        return "<li {$className}><a class=\"J_ignore\" href=\"{$url}\">{$this->label}</a></li>";
    }

    /**
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
}
