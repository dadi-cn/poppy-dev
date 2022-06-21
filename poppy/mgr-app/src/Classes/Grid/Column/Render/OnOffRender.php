<?php

namespace Poppy\MgrApp\Classes\Grid\Column\Render;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use function collect;
use function url;

/**
 * 将文件渲染为可下载的
 */
class OnOffRender extends Render
{

    protected string $type = 'download';

    public function render($server = ''): Jsonable
    {
        if ($this->value instanceof Arrayable) {
            $this->value = $this->value->toArray();
        }

        return collect((array) $this->value)->filter()->map(function ($value) use ($server) {
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
    }
}
