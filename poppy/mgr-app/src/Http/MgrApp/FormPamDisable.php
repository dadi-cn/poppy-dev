<?php

namespace Poppy\MgrApp\Http\MgrApp;

use Illuminate\Support\Facades\Route;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Widgets\FormWidget;
use Poppy\System\Action\Pam;

class FormPamDisable extends FormWidget
{
    protected string $title = '账号禁用';

    public function handle()
    {
        $date   = input('datetime', '');
        $reason = input('reason', '');
        $id     = (int) Route::input('id');
        $Pam    = (new Pam())->setPam(request()->user());
        if (!$Pam->disable($id, $date, $reason)) {
            return Resp::error($Pam->getError());
        }
        return Resp::success('当前用户已封禁', 'motion|grid:reload');
    }


    public function form()
    {
        $this->datetime('datetime', '解禁时间')->rules([
            Rule::required(),
        ])->placeholder('选择解禁时间');
        $this->textarea('reason', '封禁原因');
    }

    public function data(): array
    {
        return [];
    }
}
