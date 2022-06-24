<?php

namespace Poppy\MgrApp\Classes\Grid\Column\Show;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Fluent;
use Poppy\MgrApp\Classes\Grid\Column\Column;

trait AsUrl
{
    /**
     * 渲染为链接
     * @param Closure|null $cb
     * @param string $target
     * @return AsText|Column
     */
    public function asLink(Closure $cb = null, string $target = '_blank'): self
    {
        $this->type = 'link';
        return $this->display(function ($value) use ($cb, $target) {
            if ($cb instanceof Closure) {
                $callback = $cb->bindTo($this);
                $href     = call_user_func_array($callback, [$this]);
            }
            else {
                $href = $cb ?: $value;
            }
            return new Fluent([
                'url'    => $href,
                'target' => $target,
                'text'   => $value,
            ]);
        });
    }


    /**
     * 下载
     * @param string $server 服务器地址
     * @return Column|AsUrl
     */
    public function asDownload($server = ''): self
    {
        $this->type = 'download';
        return $this->display(function ($value) use ($server) {
            if ($value instanceof Arrayable) {
                $value = $value->toArray();
            }
            return collect((array) $value)->filter()->map(function ($value) use ($server) {
                if (empty($value)) {
                    return [];
                }

                if (url()->isValidUrl($value)) {
                    $src = $value;
                }
                elseif ($server) {
                    $src = rtrim($server, '/') . '/' . ltrim($value, '/');
                }
                else {
                    $src = $value;
                }

                $name = basename($value);
                return [
                    'title' => $name,
                    'src'   => $src,
                ];
            });
        });
    }
}
