<?php

namespace Poppy\Framework\Classes;

use Faker\Factory;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Poppy\Framework\Helper\UtilHelper;

class Mocker
{

    private static $factory;

    /**
     * Mocker 生成器
     * {
     *     "name" : "name"
     * }
     * @param mixed  $json
     * @param string $locale
     * @return array
     */
    public static function generate($json, $locale = Factory::DEFAULT_LOCALE): array
    {
        self::$factory = Factory::create($locale);

        if (is_string($json)) {
            if (!UtilHelper::isJson($json)) {
                return [];
            }
            $define = json_decode($json, true);
        }
        else if (is_array($json)) {
            $define = $json;
        }
        else {
            return [];
        }

        $gen = [];
        foreach ($define as $dk => $def) {
            $gen[$dk] = self::parseValue($def);
        }
        return $gen;
    }


    /**
     * @param $value
     * @return mixed
     */
    private static function parseValue($value)
    {
        if (Str::contains($value, '|')) {
            [$prop, $params] = explode('|', $value);
        }
        else {
            $prop   = $value;
            $params = '';
        }
        $arrParam = explode(',', $params);
        try {
            if ($params) {
                return call_user_func_array([self::$factory, $prop], $arrParam);
            }
            return call_user_func([self::$factory, $prop]);
        } catch (InvalidArgumentException  $e) {
            return $e->getMessage();
        }
    }
}