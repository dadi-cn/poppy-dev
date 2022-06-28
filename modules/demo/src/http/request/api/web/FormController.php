<?php

namespace Demo\Http\Request\Api\Web;

use Illuminate\Support\Str;
use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Widgets\FormWidget;
use Poppy\System\Http\Request\ApiV1\WebApiController;

class FormController extends WebApiController
{

    public function auto($auto)
    {
        $type  = ucfirst(Str::camel($auto));
        $class = "Demo\App\Forms\Form{$type}Establish";
        /** @var FormWidget $form */
        $form = new $class();
        $form->title($type);
        return $form->resp();
    }

    public function cascader()
    {
        return Resp::success('cascader', [
            [
                'label' => '接口数据',
                'value' => 'gen',
            ],
        ]);
    }

    public function ctrl()
    {
        $form = new FormWidget();
        $form->text('name', '姓名');
        $form->data([
            'name' => '多厘',
        ]);
        $form->on(function () {
            return Resp::error(var_export(input(), true));
        });
        return $form->resp();
    }
}
