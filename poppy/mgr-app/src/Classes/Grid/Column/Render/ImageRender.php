<?php

namespace Poppy\MgrApp\Classes\Grid\Column\Render;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Str;
use function collect;
use function url;

class ImageRender extends Render
{

    protected string $type = 'image';

    public function render($server = '', $width = 60, $height = 60, $suffix = ''): Jsonable
    {
        if ($this->value instanceof Arrayable) {
            $this->value = $this->value->toArray();
        }

        return collect((array) $this->value)->filter()->map(function ($path) use ($server, $width, $height, $suffix) {
            if (url()->isValidUrl($path) || strpos($path, 'data:image') === 0) {
                $src = $path;
            } elseif ($server) {
                $src = rtrim($server, '/') . '/' . ltrim($path, '/');
            } else {
                $src = $path;
            }
            return [
                'thumb'  => Str::contains($src, ['?', '!']) ? $src : $src . '?' . $suffix,
                'origin' => Str::contains($src, ['?', '!']) ? $src : $src . '?' . $suffix,
                'width'  => $width,
                'height' => $height,
            ];
        });
    }
}
