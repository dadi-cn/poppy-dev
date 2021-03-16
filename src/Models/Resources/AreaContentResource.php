<?php

namespace Poppy\Area\Models\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

/**
 * 地区resource
 */
class AreaContentResource extends Resource
{
    /**
     * 将资源转换成数组。
     * @param Request $request request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'title'     => $this->title,
            'parent_id' => $this->parent_id,
        ];
    }
}