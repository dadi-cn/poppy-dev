<?php

namespace Poppy\Sms\Http\MgrApp;

use Poppy\MgrApp\Classes\Filter\FilterPlugin;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Grid\Tools\Interactions;
use Poppy\MgrApp\Classes\Table\Render\GridActions;
use Poppy\MgrApp\Classes\Table\Render\Render;
use Poppy\MgrApp\Classes\Table\TablePlugin;
use function collect;
use function data_get;
use function route;
use function sys_hook;

class GridSms extends GridBase
{

    public string $title = '短信模版';

    /**
     */
    public function table(TablePlugin $table)
    {
        $table->add('type', "类型")->quickTitle();
        $table->add('description', '描述')->quickTitle()->display(function () {
            $types = collect(config('poppy.sms.types'));
            return $types->where('type', data_get($this, 'type'))->first()['title'] ?? '';
        });
        $table->add('code', "短信码/内容")->ellipsis();
        $table->add('handle')->asAction(function (GridActions $actions) {
            /** @var Render $this */
            $item = $this->getRow();
            $actions->quickIcon();
            $actions->request('删除', route('py-sms:api-backend.sms.delete', data_get($item, 'scope') . ':' . data_get($item, 'type')))
                ->icon('Close')->danger()->confirm();
        })->quickIcon(1);
    }


    public function filter(FilterPlugin $filter)
    {
        $sendTypes = sys_hook('poppy.sms.send_type');
        foreach ($sendTypes as $type => $def) {
            $filter->scope($type, $def['title']);
        }
    }

    public function quick(Interactions $actions)
    {
        $actions->page('新建模板', route('py-sms:api-backend.sms.establish'), 'form');
    }
}
