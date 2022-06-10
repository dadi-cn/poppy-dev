<?php

namespace Demo\App\Forms;

use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Widgets\FormWidget;

class FormFieldDynamicEstablish extends FormWidget
{

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
            'country'  => 'HK',
            'province' => 4,
            'city'     => 50,
        ];
    }

    public function form()
    {
        $this->select('country', '地区')->depend('area', 'type|country')->filterable();
        $this->select('province')->depend('area', 'type|province')->filterable();
        $this->dynamic('city', '子地区')->rel(['province'])->depend('area', 'type|city');
        $this->code('default-code');
    }

    /**
     * 定义条件依赖
     * @return void
     */
    public function dependence()
    {

    }
}
