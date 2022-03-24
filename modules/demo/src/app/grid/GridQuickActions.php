<?php

namespace Demo\App\Grid;

use Poppy\MgrApp\Classes\Grid\Column\Render\ActionsRender;
use Poppy\MgrApp\Classes\Grid\Filter\Query\Scope;
use Poppy\MgrApp\Classes\Grid\Tools\Actions;
use Poppy\MgrApp\Classes\Widgets\FilterWidget;
use Poppy\MgrApp\Classes\Widgets\TableWidget;
use Poppy\MgrApp\Http\Grid\GridBase;

/**
 * 按钮
 */
class GridQuickActions extends GridBase
{
    /**
     * @inheritDoc
     */
    public function table(TableWidget $table)
    {
        $table->add('id');
        $table->add('type', 'TYPE')->display(function () {
            if (data_get($this, 'id') % 2 === 0) {
                return '分组';
            } else {
                return '散开';
            }
        });
        $table->action(function (ActionsRender $actions) {
            $row = $actions->getRow();
            $actions->request('错误', route('demo:api.mgr_app.grid_request', ['error']));
            $actions->request('成功', route('demo:api.mgr_app.grid_request', ['success']));
            $actions->request('确认', route('demo:api.mgr_app.grid_request', ['success']))->confirm();
            $actions->request('Disabled', route('demo:api.mgr_app.grid_request', ['success']))->disabled();
            $actions->request('Plain', route('demo:api.mgr_app.grid_request', ['success']))->plain();
            $actions->request('Primary', route('demo:api.mgr_app.grid_request', ['success']))->primary();
            $actions->request('Plain', route('demo:api.mgr_app.grid_request', ['success']))->primary()->plain();
            $actions->page('页面', route('demo:api.mgr_app.grid_form', ['detail']), 'form');
        });
    }


    /**
     * @inheritDoc
     */
    public function filter(FilterWidget $filter)
    {
        $filter->like('username', 'username');
    }

    public function quick(Actions $actions)
    {
        $scope = input(Scope::QUERY_NAME);
        $actions->page('新建', 'abc', 'form');
    }
}
