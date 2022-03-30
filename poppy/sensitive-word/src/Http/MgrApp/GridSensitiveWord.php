<?php

namespace Poppy\SensitiveWord\Http\MgrApp;

use Poppy\MgrApp\Classes\Grid\Column\Render\ActionsRender;
use Poppy\MgrApp\Classes\Grid\Column\Render\Render;
use Poppy\MgrApp\Classes\Grid\Tools\Actions;
use Poppy\MgrApp\Classes\Widgets\FilterWidget;
use Poppy\MgrApp\Classes\Widgets\TableWidget;
use Poppy\MgrApp\Classes\Grid\GridBase;
use function route;
use function route_url;

class GridSensitiveWord extends GridBase
{

    public string $title = '敏感词';

    /**
     */
    public function table(TableWidget $table)
    {
        $table->add('id', "ID")->sortable()->quickId();
        $table->add('word', "敏感词");
        $table->action(function (ActionsRender $actions) {
            /** @var $this Render */
            $row = $this->getRow();
            $actions->quickIcon();
            $actions->request('删除', route_url('py-sensitive-word:api-backend.word.delete', data_get($row, 'id')))
                ->icon('Close')->danger();
        })->quickIcon(1);
    }


    public function filter(FilterWidget $filter)
    {
        $filter->like('word', '敏感词');
    }

    public function batch(Actions $actions)
    {
        $actions->request('删除', route('py-sensitive-word:api-backend.word.delete'))
            ->icon('Close')->danger()->confirm();
    }
}
