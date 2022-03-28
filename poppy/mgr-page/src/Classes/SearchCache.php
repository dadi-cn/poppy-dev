<?php

namespace Poppy\MgrPage\Classes;

use Overtrue\Pinyin\Pinyin;
use Poppy\Core\Redis\RdsDb;

/**
 * 搜索缓存
 */
class SearchCache
{
    /**
     * @param string $text
     * @return string
     */
    public static function py(string $text): string
    {
        static $pinyin;
        $Rds = new RdsDb();
        if (class_exists('Overtrue\Pinyin\Pinyin')) {
            if ($py = $Rds->hget(PyMgrPageDef::ckTagSearchPy(), $text)) {
                return $py;
            }
            if (!$pinyin) {
                $pinyin = new Pinyin();
            }
            /** @var  $pinYin */
            $py = $pinyin->abbr($text);
            $Rds->hset(PyMgrPageDef::ckTagSearchPy(), $text, $py);
            return $py;
        }
        return '';
    }
}
