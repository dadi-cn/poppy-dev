<?php

namespace Poppy\MgrPage\Classes;

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
        $Rds = new RdsDb();
        if (function_exists('ext_pinyin_abbr' && class_exists('Overtrue\Pinyin\Pinyin'))) {
            if ($py = $Rds->hget(PyMgrPageDef::ckTagSearchPy(), $text)) {
                return $py;
            }
            /** @var  $pinYin */
            $py = ext_pinyin_abbr($text);
            $Rds->hset(PyMgrPageDef::ckTagSearchPy(), $text, $py);
            return $py;
        }
        return '';
    }
}
