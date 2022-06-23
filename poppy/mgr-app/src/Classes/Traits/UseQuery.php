<?php

namespace Poppy\MgrApp\Classes\Traits;

use Illuminate\Support\Str;

trait UseQuery
{

    /**
     * 检测查询类型是否存在
     * @param string $query 查询内容
     * @param string $type  查询类型
     * @return bool
     */
    protected function queryHas(string $query, string $type): bool
    {
        $allTypes = explode(',', $query);
        $arrTypes = collect($allTypes)->map(function ($item) {
            return Str::before($item, ':');
        });
        return in_array($type, $arrTypes->toArray());
    }

    /**
     * 检测查询类型是否存在
     * @param string $query 查询内容
     * @param string $type  查询类型
     * @return string
     */
    protected function queryAfter(string $query, string $type): string
    {
        $allTypes = explode(',', $query);
        $queries  = [];
        collect($allTypes)->each(function ($item) use (&$queries) {
            $type  = Str::before($item, ':');
            $query = '';
            if (Str::contains($item, ':')) {
                $type  = Str::before($item, ':');
                $query = Str::after($item, ':');
            }
            $queries[$type] = $query;
        });
        return $queries[$type] ?? '';
    }
}
