<?php

namespace Poppy\MgrApp\Classes\Dashboard;

use Closure;
use Exception;
use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Form\FormPlugin;
use ReflectionFunction;

/**
 * 用于同一个 Scope 下 / 无 Scope 下的面型表单配置
 */
class PanelForm extends PanelBase
{

    /**
     * 表单定义
     * @var FormPlugin
     */
    private FormPlugin $form;

    /**
     * 处理回调
     * @var Closure
     */
    private Closure $handleCb;


    public function __construct($title, $width)
    {
        parent::__construct($title, $width);
        $this->form = new FormPlugin();
        $this->form->title($this->title);
        $this->key = md5($this->title);
    }

    public function form($cb): self
    {
        try {
            $ref       = new ReflectionFunction($cb);
            $this->key = md5($ref->getFileName() . $ref->getStartLine() . $ref->getEndLine());
        } catch (Exception $e) {
            // not modify key
        }
        $cb->call($this, $this->form);
        return $this;
    }

    /**
     * 默认数据
     * @param array $data
     * @return $this
     */
    public function data(array $data): self
    {
        $this->form->fill($data);
        return $this;
    }

    public function handle(Closure $cb): self
    {
        $this->handleCb = $cb;
        return $this;
    }

    /**
     * 最后的回调
     */
    public function resp(array $data = [])
    {
        if ($messageBag = $this->form->validate($data)) {
            return Resp::error($messageBag);
        }
        return $this->handleCb->call($this, $data);
    }

    public function struct($query = ''): array
    {
        return array_merge($this->form->struct($query), [
            'width' => $this->width,
            'title' => $this->title,
        ]);
    }
}
