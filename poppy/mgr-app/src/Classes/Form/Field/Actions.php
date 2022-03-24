<?php

namespace Poppy\MgrApp\Classes\Form\Field;

use Poppy\MgrApp\Classes\Action\Action;
use Poppy\MgrApp\Classes\Action\PageAction;
use Poppy\MgrApp\Classes\Form\FormItem;
use function tap;

class Actions extends FormItem
{
    protected bool $toModel = false;

    /**
     * @var array
     */
    private array $actions = [];


    public function attributes(): object
    {
        $actions = [];
        foreach ($this->actions as $append) {
            if ($append instanceof Action) {
                $def       = $append->struct();
                $actions[] = $def;
            }
        }

        $this->setAttribute('actions', $actions);
        return parent::attributes();
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
        return tap($action, function () use ($action) {
            $this->add($action);
        });
    }

    /**
     * Append an action.
     *
     * @param array|Action $action
     */
    private function add($action)
    {
        if (is_array($action)) {
            $this->actions = array_merge($this->actions, $action);
        } else {
            $this->actions[] = $action;
        }
    }
}
