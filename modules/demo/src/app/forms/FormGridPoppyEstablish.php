<?php

namespace Demo\App\Forms;

use Poppy\Area\Action\Area;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Widgets\FormWidget;

class FormGridPoppyEstablish extends FormWidget
{

    /**
     * 设置id
     * @param $id
     * @return $this
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }


    public function handle()
    {
        $id   = input('id');
        $Area = new Area();
        if (is_post()) {
            if ($Area->establish(input(), $id)) {
                return Resp::success('添加版本成功', '_top_reload|1');
            }
            return Resp::error($Area->getError());
        }

        $id && $Area->initArea($id) && $Area->share();
    }

    public function data(): array
    {
        return [
            'username' => 5,
        ];
    }

    public function form()
    {
        $this->text('username', '用户名')->rules([
            Rule::required(),
        ]);
        $this->text('password', '密码')->rules([
            Rule::required(),
        ]);
    }
}
