<?php

namespace Poppy\Framework\Classes;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Poppy\Framework\Helper\UtilHelper;
use Throwable;

class Mocker
{

    private static Generator $factory;

    /**
     * Mocker 生成器
     * {
     *     "name" : "name"
     * }
     * @param string|array $json
     * @param string $locale
     * @return array
     */
    public static function generate($json, string $locale = Factory::DEFAULT_LOCALE): array
    {
        self::$factory = Factory::create($locale);

        if (Arr::isAssoc($json)) {
            $define = (object) $json;
        }
        else {
            if (!UtilHelper::isJson($json)) {
                return ['Input Mock Is Not Valid Json'];
            }
            $define = json_decode($json);
        }
        $gen = [];
        if (is_array($define)) {
            return self::parseValue($define, 15);
        }
        foreach ($define as $dk => $def) {
            [$key, $num] = self::parseKey($dk);
            $gen[$key] = self::parseValue($def, $num);
        }
        return $gen;
    }


    /**
     * 解析KEY/NUM
     * @param string $key
     * @return array
     */
    private static function parseKey(string $key): array
    {
        $parsed = explode('|', $key);
        $pk     = $parsed[0];
        $num    = $parsed[1] ?? 1;
        if (Str::contains($num, '-')) {
            [$start, $end] = explode('-', $num);
            $num = rand($start, $end);
        }
        return [$pk, $num];
    }

    /**
     * @param string|array $value
     * @return mixed
     */
    private static function parseValue($value, $num = 1)
    {
        if (is_array($value)) {
            $first = Arr::first($value);
            // 对象
            if (is_object($first)) {
                $item = [];
                for ($i = 0; $i < $num; $i++) {
                    $firstParse = [];
                    foreach ((array) $first as $k => $v) {
                        [$kk, $kn] = self::parseKey($k);
                        $firstParse[$kk] = self::parseValue($v, $kn);
                    }
                    $item [] = (object) $firstParse;
                }
                return $item;

            }
            // 字串
            if (is_string($first)) {
                $item = [];
                for ($i = 0; $i < $num; $i++) {
                    $item [] = self::parseValue($first);
                }
                return $item;
            }
        }

        if (is_object($value)) {
            $object = [];
            foreach ((array) $value as $k => $v) {
                $object[$k] = self::parseValue($v);
            }
            return (object) $object;
        }

        // 字串
        if (preg_match('/(?<method>.+?)\((?<param>.*?)\)/', $value, $match)) {
            $prop   = $match['method'];
            $params = json_decode('[' . $match['param'] . ']');
        }
        elseif (Str::contains($value, '|')) {
            [$prop, $strParams] = explode('|', $value);
            $params = json_decode('[' . $strParams . ']');
        }
        else {
            $prop   = $value;
            $params = [];
        }

        try {
            if (is_numeric($prop) || !preg_match('/^[a-zA-Z0-9]+$/', $prop)) {
                return str_repeat($prop, $num);
            }
            $res = call_user_func_array([self::$factory, $prop], $params);
            if (is_string($res)) {
                return str_repeat($res, $num);
            }
            return $res;
        } catch (Throwable $e) {
            return str_repeat($prop, $num);
        }
    }
}
