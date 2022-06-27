<?php

namespace Demo\Http\Request\Api\Web;

use Illuminate\Support\Str;
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
}
