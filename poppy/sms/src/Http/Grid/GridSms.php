<?php

namespace Poppy\Sms\Http\Grid;

use Poppy\MgrApp\Classes\Grid\Column\Render\ActionsRender;
use Poppy\MgrApp\Classes\Grid\Column\Render\Render;
use Poppy\MgrApp\Classes\Grid\Tools\Actions;
use Poppy\MgrApp\Classes\Widgets\FilterWidget;
use Poppy\MgrApp\Classes\Widgets\TableWidget;
use Poppy\MgrApp\Http\Grid\GridBase;

class GridSms extends GridBase
{

    public string $title = '短信模版';

    /**
     */
    public function table(TableWidget $table)
    {
        $table->add('type', "类型")->quickTitle();
        $table->add('description', '描述')->quickTitle()->display(function () {
            $types = collect(config('poppy.sms.types'));
            return $types->where('type', data_get($this, 'type'))->first()['title'] ?? '';
        });
        $table->add('code', "短信码/内容")->ellipsis();
        $table->action(function (ActionsRender $actions) {
            /** @var Render $this */
            $item = $this->getRow();
            $actions->quickIcon();
            $actions->request('删除', route('py-sms:api-backend.sms.delete', data_get($item, 'scope') . ':' . data_get($item, 'type')))
                ->icon('Close')->danger()->confirm();
        })->quickIcon(1);
    }


    public function filter(FilterWidget $filter)
    {
        $sendTypes = sys_hook('poppy.sms.send_type');
        foreach ($sendTypes as $type => $def) {
            $filter->scope($type, $def['title']);
        }
    }

    public function quick(Actions $actions)
    {
        $actions->page('新建模板', route('py-sms:api-backend.sms.establish'), 'form');
    }
}
