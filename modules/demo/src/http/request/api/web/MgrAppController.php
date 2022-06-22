<?php

namespace Demo\Http\Request\Api\Web;

use Demo\App\Forms\FormGridPoppyEstablish;
use Demo\Models\DemoWebapp;
use Demo\Models\DemoWebappNpk;
use Illuminate\Support\Str;
use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Widgets\FormWidget;
use Poppy\MgrApp\Classes\Widgets\GridWidget;
use Poppy\System\Http\Request\ApiV1\WebApiController;

class MgrAppController extends WebApiController
{

    /**
     * @api                    {get} api/demo/form/:auto   [Demo]Form
     * @apiVersion             1.0.0
     * @apiName                Form
     * @apiQuery               {string} auto 表单名称
     * @apiGroup               MgrApp
     */
    public function form($auto)
    {
        $type  = ucfirst(Str::camel($auto));
        $class = "Demo\App\Forms\Form{$type}Establish";
        /** @var FormWidget $form */
        $form = new $class();
        $form->title($type);
        return $form->resp();
    }

    /**
     * @api                    {get} api/demo/grid/:auto   [Demo]Grid
     * @apiVersion             1.0.0
     * @apiName                Grid
     * @apiQuery               {string} auto 列表名称
     * @apiGroup               MgrApp
     */
    public function grid($type)
    {
        // 第一列显示id字段，并将这一列设置为可排序列
        $grid = new GridWidget(new DemoWebapp());
        $grid->setTitle('Title');
        $classname = '\Demo\App\Grid\Grid' . Str::studly($type);
        $grid->setLists($classname);
        return $grid->resp();
    }

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
     * @api                    {get} api/demo/grid_request/:type   GridRequest
     * @apiVersion             1.0.0
     * @apiName                GridRequest
     * @apiQuery               {string} type 请求名称
     * @apiGroup               MgrApp
     */
    public function gridRequest($type)
    {
        if ($type === 'error') {
            return Resp::error('请求错误');
        }
        else {
            return Resp::success('请求成功', input());
        }
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

    /**
     * @api                    {get} api/demo/grid_form   GridForm
     * @apiVersion             1.0.0
     * @apiName                Grid表单
     * @apiGroup               MgrApp
     */
    public function gridForm($type)
    {
        $form = new FormGridPoppyEstablish();
        $form->title($type, 'Grid:Form');
        return $form->resp();
    }
}
