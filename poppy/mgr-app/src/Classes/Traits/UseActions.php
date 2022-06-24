<?php

namespace Poppy\MgrApp\Classes\Traits;

use Poppy\MgrApp\Classes\Action\Action;
use Poppy\MgrApp\Classes\Action\IframeAction;
use Poppy\MgrApp\Classes\Action\TargetAction;

trait UseActions
{

    /**
     * @var array
     */
    protected array $items = [];

    /**
     * 默认样式
     * @var array
     */
    protected array $defaultStyle = [];


    /**
     * 下拉菜单使用 icon
     * @var bool
     */
    protected bool $dropdownIcon = false;


    /**
     * Append an action.
     *
     * @param array|Action $action
     *
     * @return $this
     */
    public function add($action): self
    {
        if (is_array($action)) {
            $this->items = array_merge($this->items, $action);
        }
        else {
            $this->items[] = $action;
        }
        return $this;
    }


    /**
     * 设置默认样式, 该样式需是可以调用的 Action 方法
     * @param array $style
     * @return $this
     */
    public function default(array $style = []): self
    {
        $this->defaultStyle = $style;
        return $this;
    }

    public function quickIcon(): self
    {
        $this->defaultStyle = ['plain', 'only', 'circle'];
        return $this;
    }

    /**
     * 请求
     * @param string $title
     * @param string $url
     * @return TargetAction
     */
    public function target(string $title, string $url): TargetAction
    {
        $action = new TargetAction($title, $url);
        $action = $this->useDefaultStyle($action);
        return tap($action, function () use ($action) {
            $this->add($action);
        });
    }

    /**
     * 请求
     * @param string $title
     * @param string $url
     * @return IframeAction
     */
    public function iframe(string $title, string $url): IframeAction
    {
        $action = new IframeAction($title, $url);
        $action = $this->useDefaultStyle($action);
        return tap($action, function () use ($action) {
            $this->add($action);
        });
    }

    /**
     * 调用默认的样式
     * @param $action
     * @return mixed
     */
    protected function useDefaultStyle($action)
    {
        if (count($this->defaultStyle)) {
            foreach ($this->defaultStyle as $style) {
                if (is_callable([$action, $style])) {
                    $action = call_user_func([$action, $style]);
                }
            }
        }
        return $action;
    }
}
