<?php

namespace Poppy\Ad\Models\Filters;

use EloquentFilter\ModelFilter;

/**
 * 广告位
 */
class AdPlaceFilter extends ModelFilter
{
    /**
     * 根据ID搜索
     * @param int $id 广告位ID
     * @return AdPlaceFilter
     */
    public function id($id): self
    {
        return $this->where('id', $id);
    }

    /**
     * 根据标题搜索
     * @param string $title 广告位标题
     * @return AdPlaceFilter
     */
    public function title($title): self
    {
        return $this->where('title', 'like', '%' . $title . '%');
    }
}