<?php

namespace Poppy\MgrApp\Classes\Traits;

use Poppy\MgrApp\Classes\Action\CopyAction;
use Poppy\MgrApp\Classes\Action\PageAction;
use Poppy\MgrApp\Classes\Action\ProgressAction;
use Poppy\MgrApp\Classes\Action\RequestAction;

trait UseInteraction
{

    /**
     * 样式
     * @var string
     */
    protected string $style = '';

    /**
     * @var int
     */
    protected int $length = 5;

    /**
     * 下拉样式
     * @param int $length 长度
     * @param bool $icon 图标
     * @return self
     */
    public function styleDropdown(int $length = 5, bool $icon = false): self
    {
        $this->style  = 'dropdown';
        $this->length = $length;
        if ($icon) {
            $this->dropdownIcon = $icon;
        }

        return $this;
    }


    /**
     * 请求
     * @param string $title
     * @param string $url
     * @return RequestAction
     */
    public function request(string $title, string $url): RequestAction
    {
        $action = new RequestAction($title, $url);
        $action = $this->useDefaultStyle($action);
        return tap($action, function () use ($action) {
            $this->add($action);
        });
    }

    /**
     * 页面
     * @param string $title
     * @param string $url
     * @param string $type
     * @return PageAction
     */
    public function page(string $title, string $url, string $type): PageAction
    {
        $action = (new PageAction($title, $url))->type($type);
        $action = $this->useDefaultStyle($action);
        return tap($action, function () use ($action) {
            $this->add($action);
        });
    }

    /**
     * 复制内容
     * @param string $title
     * @param string $content
     * @return CopyAction
     */
    public function copy(string $title, string $content): CopyAction
    {
        $action = new CopyAction($title, $content);
        $action = $this->useDefaultStyle($action);
        return tap($action, function () use ($action) {
            $this->add($action);
        });
    }


    /**
     * 进度
     * @param string $title
     * @param string $url
     * @return ProgressAction
     */
    public function progress(string $title, string $url): ProgressAction
    {
        $action = (new ProgressAction($title, $url));
        $action->icon('mu:donut_large');
        $action = $this->useDefaultStyle($action);
        return tap($action, function () use ($action) {
            $this->add($action);
        });
    }
}
