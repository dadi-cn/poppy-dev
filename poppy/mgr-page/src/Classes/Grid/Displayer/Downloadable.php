<?php

namespace Poppy\MgrPage\Classes\Grid\Displayer;

use Illuminate\Contracts\Support\Arrayable;
use function collect;
use function url;

/**
 * 将文件渲染为可下载的
 */
class Downloadable extends AbstractDisplayer
{
    public function display($server = '')
    {
        if ($this->value instanceof Arrayable) {
            $this->value = $this->value->toArray();
        }

        return collect((array) $this->value)->filter()->map(function ($value) use ($server) {
            if (empty($value)) {
                return '';
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
            return <<<HTML
<a href="$src" download="$name" target="_blank" class="J_tooltip" title="$name">
    <i class="fa fa-download"></i>
</a>
HTML;
        })->implode('&nbsp;');
    }
}
