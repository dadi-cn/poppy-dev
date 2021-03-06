<?php

namespace Poppy\Ad\Http\MgrApp;

use Poppy\Ad\Models\AdPlace;
use Poppy\MgrApp\Classes\Filter\FilterPlugin;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Grid\Tools\Interactions;
use Poppy\MgrApp\Classes\Table\Render\GridActions;
use Poppy\MgrApp\Classes\Table\Render\Render;
use Poppy\MgrApp\Classes\Table\TablePlugin;
use function collect;
use function data_get;
use function route;

class GridAdPlace extends GridBase
{

    public string $title = '短信模版';

    /**
     */
    public function table(TablePlugin $table)
    {
        $table->add('id', "类型")->quickId();
        $table->add('title', "名称")->quickId();
        $table->add('thumb', "示意图")->asImage();
        $table->add('introduce', "说明");
        $table->add('size', "尺寸")->display(function () {
            /** @var $this AdPlace */
            return $this->width . 'x' . $this->height;
        });

        $table->add('description', '描述')->quickTitle()->display(function () {
            $types = collect(config('poppy.sms.types'));
            return $types->where('type', data_get($this, 'type'))->first()['title'] ?? '';
        });
        $table->add('code', "短信码/内容")->ellipsis();
        $table->add('handle', '操作')->asAction(function (GridActions $actions) {
            /** @var Render $this */
            $item = $this->getRow();
            $actions->quickIcon();
            $actions->request('删除', route('py-sms:api-backend.sms.delete', data_get($item, 'scope') . ':' . data_get($item, 'type')))
                ->icon('Close')->danger()->confirm();
        })->quickIcon(1);
    }


    public function filter(FilterPlugin $filter)
    {
        $filter->like('title', '标题');
    }

    public function quick(Interactions $actions)
    {
        $actions->page('新建广告位', route('py-ad:mgr-app.place.establish'), 'form');
    }
}
