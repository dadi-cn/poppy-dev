<?php

namespace Poppy\MgrPage\Classes\Form\_Back;

use Poppy\MgrPage\Classes\Form\Field;
use function env;
use function Poppy\System\Classes\Form\Field\config;

class Map extends Field
{
    /**
     * Column name.
     *
     * @var array
     */
    protected $column = [];

    /**
     * Get assets required by this field.
     *
     * @return array
     */
    public static function getAssets()
    {
        switch (config('admin.map_provider')) {
            case 'tencent':
                $js = '//map.qq.com/api/js?v=2.exp&key=' . env('TENCENT_MAP_API_KEY');
                break;
            case 'google':
                $js = '//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&key=' . env('GOOGLE_API_KEY');
                break;
            case 'yandex':
                $js = '//api-maps.yandex.ru/2.1/?lang=ru_RU';
                break;
            default:
                $js = '//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&key=' . env('GOOGLE_API_KEY');
        }

        return compact('js');
    }

    public function __construct($column, $arguments)
    {
        $this->column['lat'] = (string) $column;
        $this->column['lng'] = (string) $arguments[0];

        array_shift($arguments);

        $this->label = $this->formatLabel($arguments);
        $this->id    = $this->formatId($this->column);
    }

}
