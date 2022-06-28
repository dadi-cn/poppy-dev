<?php

namespace Poppy\MgrApp\Classes\Widgets;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\MessageBag;
use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Form\FormPlugin;
use function app;

/**
 * Form 表单
 */
class FormWidget extends FormPlugin
{

    /**
     * @var array
     */
    private array $data = [];

    private ?Closure $cb;

    public function form()
    {

    }

    public function with($data = []): self
    {
        if ($data) {
            $this->data = array_merge($this->data, $data);
        }
        return $this;
    }

    public function on(Closure $cb): self
    {
        $this->cb = $cb;
        return $this;
    }

    public function handle()
    {
        if ($this->cb) {
            return $this->cb->call($this);
        }
        return Resp::success('默认请求');
    }

    public function data(): array
    {
        if ($this->data) {
            return $this->data;
        }
        return [];
    }

    /**
     * 初始化 widget 的信息
     * @return self
     */
    public function initWidget(): self
    {
        $this->form();
        return $this->fill($this->data());
    }


    /**
     * 返回表单的结构
     * 规则解析参考 : https://github.com/yiminghe/async-validator
     * @return JsonResponse|RedirectResponse|Response
     */
    public function resp()
    {
        $request = app('request');
        // 组建 Form 表单
        $this->initWidget();

        $query = input('_query');
        if ($this->queryHas($query, 'submit')) {
            $message = $this->validate($request->all());
            if ($message instanceof MessageBag) {
                return Resp::error($message);
            }
            return $this->handle();
        }

        $struct = $this->struct($query);
        return Resp::success(input('_query') ?: '', $struct);
    }
}
