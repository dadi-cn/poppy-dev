<?php

namespace Poppy\MgrApp\Classes\Form\Traits;

use Poppy\MgrApp\Classes\Action\TableAction;

trait UseTableAction
{

    /**
     * 删除
     * @param string $title
     * @param string $icon
     * @return TableAction
     */
    public function deleteRow(string $title, string $icon = ''): TableAction
    {
        $button = new TableAction($title, '');
        $button->icon('mu:backspace');
        $button->action('delete');
        if ($icon) {
            $button->icon($icon);
        }
        return $this->addTableAction($icon, $button);
    }

    /**
     * 拖拽排序
     * @param string $title
     * @param string $icon
     * @return TableAction
     */
    public function drapRow(string $title, string $icon = ''): TableAction
    {
        $button = new TableAction($title, '');
        $button->icon('mu:drag_indicator');
        $button->action('drag');
        return $this->addTableAction($icon, $button);
    }

    /**
     * 复制一行
     * @param string $title
     * @param string $icon
     * @return TableAction
     */
    public function copyRow(string $title, string $icon = ''): TableAction
    {
        $button = new TableAction($title, '');
        $button->icon('mu:content_copy');
        $button->action('copy');
        return $this->addTableAction($icon, $button);
    }

    /**
     * 增加一行
     * @param string $title
     * @param string $icon
     * @return TableAction
     */
    public function addRow(string $title, string $icon = ''): TableAction
    {
        $button = new TableAction($title, '');
        $button->icon('mu:new_label');
        $button->action('add');
        return $this->addTableAction($icon, $button);
    }

    /**
     * 添加表按钮
     * @param $icon
     * @param $button
     * @return mixed
     */
    private function addTableAction($icon, $button)
    {
        if ($icon) {
            $button->icon($icon);
        }
        $action = $this->useDefaultStyle($button);
        return tap($action, function () use ($action) {
            $this->add($action);
        });
    }
}
