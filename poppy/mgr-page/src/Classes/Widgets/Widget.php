<?php

namespace Poppy\MgrPage\Classes\Widgets;

use Illuminate\Support\Fluent;
use function input;

abstract class Widget extends Fluent
{

    public const TYPE_STAITC_TABLE = 'static-table';

    /**
     * @var string
     */
    protected $view;

    /**
     * @return mixed
     */
    abstract public function render();

    /**
     * Set view of widget.
     *
     * @param string $view
     */
    public function view(string $view)
    {
        $this->view = $view;
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @return string
     */
    public function formatAttributes(): string
    {
        $html = [];
        foreach ($this->getAttributes() as $key => $value) {
            $element = $this->attributeElement($key, $value);
            if (!is_null($element)) {
                $html[] = $element;
            }
        }

        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }

    /**
     * 是否是前端接口请求(请求架构)
     * @return bool
     */
    public function isSkeleton(): bool
    {
        return (bool) input('_skeleton');
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Build a single attribute element.
     *
     * @param string $key
     * @param string $value
     *
     * @return string
     */
    protected function attributeElement(string $key, string $value): string
    {
        if (!is_null($value)) {
            return $key . '="' . htmlentities($value, ENT_QUOTES, 'UTF-8') . '"';
        }
        return '';
    }
}
