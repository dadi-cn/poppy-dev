<?php

namespace Demo\Http\Request\Api\Web;

use Demo\Models\DemoWebapp;
use Illuminate\Support\Str;
use Poppy\Framework\Classes\Resp;
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
}
