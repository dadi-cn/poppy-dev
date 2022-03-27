<?php

namespace Poppy\MgrPage\Classes\Grid\Tools;

use Illuminate\Support\Str;
use function app;

/**
 * 创建按钮
 */
class ActionButton
{

    protected $title;


    protected $url;

    /**
     * @var array|mixed
     */
    private $attribute;


    public function __construct($btn_text, $url, $attribute = [])
    {
        $this->title     = $btn_text;
        $this->url       = $url;
        $this->attribute = $attribute;

        $class = $this->attribute['class'] ?? '';

        // 默认加入tooltip
        if (!Str::contains($class, 'J_tooltip')) {
            $class .= ' J_tooltip ';
        }
        $class .= ' J_request ';

        $this->attribute['class']     = $class;
        $this->attribute['data-url']  = $url;
        $this->attribute['lay-event'] = Str::random(4);
    }

    /**
     * Render CreateButton.
     *
     * @return string
     */
    public function render(): string
    {
        return ' ' . app('form')->button($this->title, $this->attribute) . ' ';
    }
}
