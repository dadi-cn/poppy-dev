<?php

namespace Poppy\MgrApp\Classes\Grid\Column\Show;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

trait AsImage
{
    /**
     * @param string $server
     * @param int $width
     * @param int $height
     * @param string $suffix
     * @return AsImage
     */
    public function asImage(string $server = '', int $width = 60, int $height = 60, string $suffix = ''): self
    {
        $this->type = 'image';

        return $this->display(function ($value) use ($server, $width, $height, $suffix) {
            if ($value instanceof Arrayable) {
                $value = $value->toArray();
            }

            return collect((array) $value)->filter()->map(function ($path) use ($server, $width, $height, $suffix) {
                if (url()->isValidUrl($path) || strpos($path, 'data:image') === 0) {
                    $src = $path;
                }
                elseif ($server) {
                    $src = rtrim($server, '/') . '/' . ltrim($path, '/');
                }
                else {
                    $src = $path;
                }
                return [
                    'thumb'  => Str::contains($src, ['?', '!']) ? $src : $src . '?' . $suffix,
                    'origin' => Str::contains($src, ['?', '!']) ? $src : $src . '?' . $suffix,
                    'width'  => $width,
                    'height' => $height,
                ];
            });
        });
    }
}
