<?php

namespace Poppy\MgrApp\Http\Request\ApiDev;

use Overtrue\Pinyin\Pinyin;
use Poppy\Framework\Classes\Resp;

class ApidocController extends DevelopController
{
    public function json()
    {
        $type     = input('type', 'web');
        $jsonFile = base_path('public/docs/' . $type . '/api_data.json');

        if (!app('files')->exists($jsonFile)) {
            return Resp::error('文档不存在');
        }
        $content = json_decode(file_get_contents($jsonFile), true);
        return Resp::success('获取成功', [
            'apidoc' => collect($content)->map(function ($item) {
                $item['description'] = strip_tags($item['description'] ?? '');
                if (class_exists('\Overtrue\Pinyin\Pinyin')) {
                    $item['pinyin'] = (new Pinyin)->abbr($item['title'] . ($item['description'] ?? ''));
                }
                $ppp = data_get($item, 'parameter.fields.Parameter', []);
                if (is_array($ppp)) {
                    $ppp = collect($ppp)->map(function ($item) {
                        $item['description'] = strip_tags($item['description'] ?? '');
                        return $item;
                    });
                }
                data_set($item, 'parameter.fields.Parameter', $ppp);
                return $item;
            })->toArray(),
        ]);
    }
}