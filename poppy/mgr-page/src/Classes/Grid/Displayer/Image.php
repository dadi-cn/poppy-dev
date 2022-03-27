<?php

namespace Poppy\MgrPage\Classes\Grid\Displayer;

use Illuminate\Contracts\Support\Arrayable;
use function collect;
use function url;

class Image extends AbstractDisplayer
{
    public function display($server = '', $width = 200, $height = 200)
    {
        if ($this->value instanceof Arrayable) {
            $this->value = $this->value->toArray();
        }

        return collect((array) $this->value)->filter()->map(function ($path) use ($server, $width, $height) {
            if (url()->isValidUrl($path) || strpos($path, 'data:image') === 0) {
                $src = $path;
            }
            elseif ($server) {
                $src = rtrim($server, '/') . '/' . ltrim($path, '/');
            }
            else {
                $src = $path;
            }

            return "<img src='$src' style='max-width:{$width}px;max-height:{$height}px' class='J_image_preview' />";
        })->implode('&nbsp;');
    }
}
