<?php

namespace Demo\Http\Request\Api\Web;

use Demo\App\Grid\GridCustomQuery;
use Demo\Models\DemoWebapp;
use Demo\Models\Queries\QueryDemoWebapp;
use Illuminate\Support\Str;
use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Filter\FilterPlugin;
use Poppy\MgrApp\Classes\Grid\Tools\Interactions;
use Poppy\MgrApp\Classes\Table\TablePlugin;
use Poppy\MgrApp\Classes\Widgets\GridWidget;
use Poppy\System\Http\Request\ApiV1\WebApiController;

class GridController extends WebApiController
{

    /**
     * @api                    {get} api/demo/grid/auto/:type   GridAuto
     * @apiVersion             1.0.0
     * @apiName                GridAuto
     * @apiParam               {string} type 类型
     * @apiGroup               Grid
     */
    public function auto($type)
    {
        // 第一列显示id字段，并将这一列设置为可排序列
        $grid = new GridWidget(new DemoWebapp());
        $grid->setTitle('Title');
        $classname = '\Demo\App\Grid\Grid' . Str::studly($type);
        $grid->setLists($classname);
        return $grid->resp();
    }


    /**
     * @api                    {get} api/demo/grid/request/:type   GridRequest
     * @apiVersion             1.0.0
     * @apiName                GridRequest
     * @apiParam               {string} type 请求类型
     * @apiGroup               Grid
     */
    public function request($type)
    {
        if ($type === 'error') {
            return Resp::error('请求错误');
        }
        else {
            return Resp::success('请求成功', input());
        }
    }

    /**
     * @api                    {get} api/demo/grid/custom_query   GridCustomQuery
     * @apiVersion             1.0.0
     * @apiName                GridCustomQuery
     * @apiGroup               Grid
     */
    public function customQuery()
    {
        // 第一列显示id字段，并将这一列设置为可排序列
        $grid = new GridWidget(new QueryDemoWebapp());
        $grid->setTitle('Title');
        $grid->setLists(GridCustomQuery::class);
        return $grid->resp();
    }


    /**
     * @api                    {get} api/demo/grid/ctrl   控制器模式
     * @apiVersion             1.0.0
     * @apiName                GridControl
     * @apiGroup               Grid
     */
    public function ctrl()
    {
        // 第一列显示id字段，并将这一列设置为可排序列
        $grid = new GridWidget(new QueryDemoWebapp());
        $grid->table(function (TablePlugin $table) {
            $table->add('id', 'ID');
            $table->add('title', '标题');
        });
        $grid->batch(function (Interactions $actions) {
            $actions->request('批量删除', '');
        });
        $grid->quick(function (Interactions $actions) {
            $actions->page('添加', '', 'form');
        });
        $grid->filter(function (FilterPlugin $filter) {
            $filter->gt('status', '状态')->asText();
        });
        return $grid->resp();
    }
}
