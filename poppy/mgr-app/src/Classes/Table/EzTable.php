<?php

namespace Poppy\MgrApp\Classes\Table;

/**
 */
class EzTable
{
    /**
     * 图片渲染器
     * @param string|array $image
     * @return array
     */
    public static function image($image): array
    {
        if (is_string($image)) {
            $image = [$image];
        }
        return [
            'type'  => 'image',
            'value' => $image,
        ];
    }
}
