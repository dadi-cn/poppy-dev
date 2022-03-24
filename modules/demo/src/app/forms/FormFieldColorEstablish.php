<?php

namespace Demo\App\Forms;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\FakerException;
use Poppy\MgrApp\Classes\Widgets\FormWidget;

class FormFieldColorEstablish extends FormWidget
{

    public function handle()
    {
        $message = print_r(input(), true);
        return Resp::success($message);
    }

    /**
     * @throws FakerException
     */
    public function data(): array
    {
        return [
            'id'       => 5,
            'default'  => '#cccccc',
            'alpha'    => 'rgba(1,3,8,0.3)',
            'disabled' => py_faker()->text(20),
            'default-code' =><<<CODE
\$this->color('default', '颜色');
CODE,
            'alpha-code' =><<<CODE
\$this->color('alpha', '透明度')->showAlpha();
CODE,
            'predefined-code' =><<<CODE
\$this->color('predefined', '预定义颜色')->predefine([
    '#000', '#333', '#999', 'rgba(1,3,8,0.3)',
]);
CODE,
            'disabled-code' =><<<CODE
\$this->color('disabled', '禁用')->disabled();
CODE,

        ];
    }

    public function form()
    {
        $this->color('default', '颜色');
        $this->code('default-code');
        $this->color('alpha', '透明度')->showAlpha();
        $this->code('alpha-code');
        $this->color('predefined', '预定义颜色')->predefine([
            '#000', '#333', '#999', 'rgba(1,3,8,0.3)',
        ]);
        $this->code('predefined-code');
        $this->color('disabled', '禁用')->disabled();
        $this->code('disabled-code');
    }
}
