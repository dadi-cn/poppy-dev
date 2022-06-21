<?php

namespace Demo\App\Grid;

use Poppy\MgrApp\Classes\Grid\Column\Render\ActionsRender;
use Poppy\MgrApp\Classes\Grid\GridBase;
use Poppy\MgrApp\Classes\Widgets\TableWidget;

/**
 * 按钮
 */
class GridButton extends GridBase
{
    /**
     * @inheritDoc
     */
    public function table(TableWidget $table)
    {
        $table->add('id')->quickId();
        $table->action(function (ActionsRender $actions) {
            $actions->request('成功', route('demo:api.mgr_app.grid_request', ['success']));
            $actions->request('错误', route('demo:api.mgr_app.grid_request', ['error']));
            $actions->request('确认', route('demo:api.mgr_app.grid_request', ['success']))->confirm();
            $actions->request('Disabled', route('demo:api.mgr_app.grid_request', ['success']))->disabled();
            $actions->request('Plain', route('demo:api.mgr_app.grid_request', ['success']))->plain();
            $actions->request('Primary', route('demo:api.mgr_app.grid_request', ['success']))->primary();
            $actions->request('Primary Plain', route('demo:api.mgr_app.grid_request', ['success']))->primary()->plain();
            $actions->request('图文', route('demo:api.mgr_app.grid_request', ['success']))->icon('warning');
            $actions->request('仅图标', route('demo:api.mgr_app.grid_request', ['success']))->icon('warning')->only();
            $actions->request('圆形图标', route('demo:api.mgr_app.grid_request', ['success']))->icon('warning')->circle()->only();
            $actions->page('页面', route('demo:api.mgr_app.grid_form', ['detail']), 'form');
            $actions->target('Target(百度)', 'https://www.baidu.com');
            $actions->page('Table', route('demo:api.table.index', ['simple']), 'table');
        });
    }

}
