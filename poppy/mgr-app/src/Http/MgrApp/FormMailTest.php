<?php

namespace Poppy\MgrApp\Http\MgrApp;

use Mail;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Widgets\FormWidget;
use Poppy\System\Mail\TestMail;
use Throwable;

class FormMailTest extends FormWidget
{

    protected string $title = '发送测试邮件';

    public function handle()
    {
        $all = input();
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
        $this->email('to', '邮箱')->rules([
            Rule::required(),
            Rule::email()
        ]);
        $this->textarea('content', '内容')->rules([
            Rule::required(),
        ]);
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return [
            'content' => '发送测试邮件'
        ];
    }
}
