<?php

namespace Demo\App\Forms;

use Poppy\Area\Models\SysArea;
use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Widgets\FormWidget;

class FormFieldCascaderEstablish extends FormWidget
{
    protected string $title = '级联选择';

    public function handle()
    {
        $message = print_r(input(), true);
        return Resp::success($message);
    }

    /**
     */
    public function data(): array
    {
        return [
            'province'    => [1],
            'city'        => [3, 35],
            'area'        => [3, 36, 468],
            'area-filter' => [3, 36, 468],
            'area-sub'    => [3, 36],
            'area-lazy'   => [3, 36],
        ];
    }

    public function form()
    {
        $this->cascader('province', '地区')->options(SysArea::cascader());
        $this->cascader('city', '城市')->options(SysArea::cascader('city'));
        $this->cascader('area', '地区')->options(SysArea::cascader('area'))->width(300);
        $this->cascader('area-filter', '地区(可筛选)')->options(SysArea::cascader('area'))->width(300)->filterable();
        $this->cascader('area-sub', '地区(选择两级)')->options(SysArea::cascader('area'))->width(300)->checkStrictly();
        $this->cascader('area-muti', '地区(选择两级)')->options(SysArea::cascader('area'))->width(300)->checkStrictly()->multi();
        $this->cascader('area-lazy', '地区(懒加载)')->options(SysArea::cascader('city'))->width(300)->lazy(
            route('demo:api.form.cascader')
        );
        $this->disableReset();
    }
}
