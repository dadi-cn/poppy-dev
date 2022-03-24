<?php

namespace Poppy\MgrApp\Classes\Traits;

use Poppy\MgrApp\Classes\Action\Action;
use Poppy\MgrApp\Classes\Action\PageAction;
use Poppy\MgrApp\Classes\Action\RequestAction;
use Poppy\MgrApp\Classes\Action\ProgressAction;

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
     * 样式
     * @var string
     */
    protected string $style = '';

    /**
     * @var int
     */
    protected int $length = 5;


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
        } else {
            $this->items[] = $action;
        }
        return $this;
    }

    /**
     * 快捷ICON
     * @return $this
     */
    public function styleIcon(): self
    {
        $this->default(['plain', 'circle', 'only']);
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


    public function styleDropdown($length = 5, $icon = false): self
    {
        $this->style  = 'dropdown';
        $this->length = $length;
        if ($icon) {
            $this->dropdownIcon = $icon;
        }

        return $this;
    }


    /**
     * 返回请求
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
     * 返回页面
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
     * 返回页面
     * @param string $title
     * @param string $url
     * @return ProgressAction
     */
    public function progress(string $title, string $url): ProgressAction
    {
        $action = (new ProgressAction($title, $url));
        $action->icon('WindPower');
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
