<?php

namespace Demo\Http\Request\Api\Web;

use Demo\App\Grid\GridCustomQuery;
use Demo\Models\DemoWebapp;
use Demo\Models\DemoWebappNpk;
use Demo\Models\Queries\QueryDemoWebapp;
use Illuminate\Support\Str;
use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Grid\GridPlugin;
use Poppy\MgrApp\Classes\Table\TablePlugin;
use Poppy\MgrApp\Classes\Widgets\GridWidget;
use Poppy\System\Http\Request\ApiV1\WebApiController;

class MgrAppController extends WebApiController
{


    /**
     * @api                    {get} api/demo/grid-no-pk/:type   Grid(无主键)
     * @apiVersion             1.0.0
     * @apiName                GridNoPk
     * @apiQuery               {string} auto 查询名称
     * @apiGroup               MgrApp
     */
    public function gridNoPk($type)
    {
        // 第一列显示id字段，并将这一列设置为可排序列
        $grid = new GridWidget(new DemoWebappNpk());
        $grid->setTitle('Title');
        $classname = '\Demo\App\GridNpk\Grid' . Str::studly($type);
        $grid->setLists($classname);
        return $grid->resp();
    }

    /**
     * @api                    {get} api/demo/grid-plugin   渲染方法内Grid
     * @apiVersion             1.0.0
     * @apiName                GridPlugin
     * @apiGroup               MgrApp
     */
    public function gridPlugin()
    {
        // todo 未完成, 待处理
        $Plugin = new GridPlugin();
        $Plugin->table(function (TablePlugin $table) {
            $table->add('id')->quickId();
        });
        // 第一列显示id字段，并将这一列设置为可排序列
        $grid = new GridWidget(new DemoWebappNpk());
        $grid->setTitle('Title');
        $classname = '\Demo\App\GridNpk\Grid' . Str::studly($type);
        $grid->setLists($classname);
        return $grid->resp();
    }

    /**
     * @api                    {get} api/demo/grid-model   渲染方法内Grid
     * @apiVersion             1.0.0
     * @apiName                GridModel
     * @apiGroup               MgrApp
     */
    public function gridModel()
    {
        // 第一列显示id字段，并将这一列设置为可排序列
        $grid = new GridWidget(new QueryDemoWebapp());
        $grid->setTitle('Title');
        $grid->setLists(GridCustomQuery::class);
        return $grid->resp();
    }

    /**
     * @api                    {get} api/demo/filter/:auto   [Demo]Filter
     * @apiVersion             1.0.0
     * @apiName                Filter
     * @apiQuery               {string} auto 列表名称
     * @apiGroup               MgrApp
     */
    public function filter($type)
    {
        // 第一列显示id字段，并将这一列设置为可排序列
        $grid = new GridWidget(new DemoWebapp());
        $grid->setTitle('Title');
        $classname = '\Demo\App\Filter\Filter' . Str::studly($type);
        $grid->setLists($classname);
        return $grid->resp();
    }


    /**
     * @api                    {get} api/demo/dashboard/:type   Dashboard
     * @apiVersion             1.0.0
     * @apiName                Dashboard
     * @apiGroup               MgrApp
     */
    public function dashboard($type)
    {
        $classname = '\Demo\App\Dashboard\Dashboard' . Str::studly($type);
        $pageConf  = new $classname;
        return $pageConf->resp();
    }

    /**
     * @api              {get} api/demo/grid-view   查询Grid数据
     * @apiVersion       1.0.0
     * @apiName          GridView
     * @apiQuery {string} _pk    主键
     * @apiQuery {string} _field 字段
     * @apiGroup         MgrApp
     */
    public function gridView()
    {
        $pk    = input('_pk');
        $field = input('_field');

        $value = DemoWebapp::where('id', $pk)->select($field)->value($field);
        return Resp::success('返回', $value);
    }
}
