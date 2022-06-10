<?php

namespace Demo\Forms;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Poppy\Framework\Classes\Resp;
use Poppy\MgrPage\Classes\Widgets\FormWidget;

abstract class FormBaseWidget extends FormWidget
{

    public $ajax = true;

    /**
     * Handle the form request.
     *
     * @param Request $request
     * @return Response|JsonResponse|RedirectResponse
     */
    public function handle(Request $request)
    {
        if ($this->ajax) {
            return Resp::success('提交成功', [
                '_show'   => 'dialog',
                '_append' => '提交信息 : <pre>' . var_export($request->all(), true) . '</pre>',
            ]);
        }
        else {
            dump('提交信息', $request->all());
        }

        // todo 进行返回跳转[暂时隐藏]
        // back();
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data(): array
    {
        return [
        ];
    }
}