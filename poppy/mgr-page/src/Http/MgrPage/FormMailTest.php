<?php

namespace Poppy\MgrPage\Http\MgrPage;

use Illuminate\Http\Request;
use Mail;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrPage\Classes\Widgets\FormWidget;
use Poppy\System\Mail\TestMail;
use Throwable;

class FormMailTest extends FormWidget
{

    public function handle(Request $request)
    {
        $all = $request->all();
        try {
            Mail::to($all['to'])->send(new TestMail($all['content']));
            return Resp::success('邮件发送成功');
        } catch (Throwable $e) {
            return Resp::error($e->getMessage());
        }
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->email('to', '邮箱');
        $this->textarea('content', '内容')->rules([
            Rule::nullable(),
        ]);
    }
}
