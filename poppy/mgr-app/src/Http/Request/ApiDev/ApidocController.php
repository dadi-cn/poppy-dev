<?php

namespace Poppy\MgrApp\Http\Request\ApiDev;

use Illuminate\Support\Str;
use Poppy\Framework\Classes\Resp;

class ApidocController extends DevelopController
{
    /**
     * @api                   {post} api/mgr-dev/apidoc/json 接口文档
     * @apiVersion            1.0.0
     * @apiName               ApidocJson
     * @apiGroup              Apidoc
     * @apiQuery {string}     type     类型
     */
    public function json()
    {
        $type       = input('type', 'web');
        $bundleFile = base_path('public/docs/' . $type . '/assets/main.bundle.js');

        if (!app('files')->exists($bundleFile)) {
            return Resp::error('文档不存在');
        }

        $content = app('files')->get($bundleFile);

        $jsobject = substr($content, strpos($content, '[{type:"'), strpos($content, '={name:"Acme project",version:') - strpos($content, '[{type:"'));
        $content  = Str::beforeLast($jsobject, ';');
        $types    = array_keys(config('poppy.core.apidoc'));
        $url      = [];
        foreach ($types as $tp) {
            $url[] = [
                'url'    => route_url('py-mgr-dev:api.apidoc.json', [], ['type' => $tp]),
                'active' => $type == $tp,
                'delete' => false,
                'source' => config('poppy.core.apidoc.' . $tp . '.title') ?? $tp,
            ];
        }
        return Resp::success('获取成功', [
            'apidoc'  => $content,
            'sources' => $url
        ]);
    }
}