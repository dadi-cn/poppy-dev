<?php

namespace Poppy\MgrApp\Classes\Grid;

/**
 */
class Motion
{
    public static function windowReload(): array
    {
        return [
            'motion' => 'window:reload',
        ];
    }

    public static function gridFilter($path = ''): array
    {
        return [
            'motion' => 'grid:filter',
            'path'   => $path ?: '' ,
        ];
    }

    public static function gridReset($path = ''): array
    {
        return [
            'motion' => 'grid:reset',
            'path'   => $path ?: '' ,
        ];
    }

    public static function gridReload($path = ''): array
    {
        return [
            'motion' => 'grid:reset',
            'path'   => $path ?: '' ,
        ];
    }
}
