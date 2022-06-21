<?php

namespace Poppy\MgrApp\Classes\Grid\Column\Show;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Poppy\MgrApp\Classes\Grid\Column\Column;
use Poppy\MgrApp\Classes\Grid\Column\Option\EditOption;
use Poppy\MgrApp\Classes\Grid\Column\Option\HiddenOption;

trait AsText
{


    /**
     * @var bool 是否进行文字隐藏展示
     */
    protected bool $ellipsis = false;

    /**
     * 可复制的
     * @var bool
     */
    protected bool $copyable = false;

    /**
     * 隐藏字符展示
     * @return Column
     */
    public function ellipsis(): self
    {
        $this->ellipsis = true;
        return $this;
    }

    /**
     * 可复制
     * @return Column
     */
    public function copyable(): self
    {
        $this->copyable = true;
        return $this;
    }


    public function asHtml(Closure $closure): self
    {
        $this->type = 'html';
        return $this->display($closure);
    }

    /**
     * @param Closure $closure
     * @return self
     * @deprecated
     */
    public function html(Closure $closure): self
    {
        return $this->asHtml($closure);
    }

    /**
     * 使用KV进行替换输出, 并可以指定默认值
     * @param array $values
     * @param string $default
     * @return $this
     */
    public function asKv(array $values, string $default = ''): self
    {
        return $this->display(function ($value) use ($values, $default) {
            if (is_null($value)) {
                return $default;
            }

            return Arr::get($values, $value, $default);
        });
    }


    /**
     * 隐藏数值
     * @param Closure|null $cb
     * @return HiddenOption
     */
    public function asHidden(Closure $cb = null): HiddenOption
    {
        $this->type     = 'hidden';
        $this->editable = 'hidden';
        return tap(new HiddenOption(), function ($option) use ($cb) {
            $this->setEditAttr($option);
            return $this->display(function () use ($cb) {
                $value = '';
                if ($cb instanceof Closure) {
                    $callback = $cb->bindTo($this);
                    $value    = call_user_func_array($callback, [$this]);
                }
                return new Fluent([
                    'value' => $value,
                ]);
            });
        });
    }

    /**
     * @param array $values
     * @param string $default
     * @return $this
     * @deprecated
     */
    public function usingKv(array $values, string $default = ''): self
    {
        return $this->asKv($values, $default);
    }

    /**
     * 追加编辑模式
     */
    public function editAsText(Closure $cb = null, Closure $disableCb = null): EditOption
    {
        $this->type     = 'text';
        $this->editable = 'text';
        return tap(new EditOption(), function ($option) use ($cb, $disableCb) {
            $this->setEditAttr($option);
            $this->asDefaultDisabled($cb, $disableCb);
        });
    }
}
