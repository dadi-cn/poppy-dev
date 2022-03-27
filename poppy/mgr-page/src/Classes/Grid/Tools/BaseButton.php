<?php

namespace Poppy\MgrPage\Classes\Grid\Tools;

use Html;
use Illuminate\Support\Str;

/**
 * 创建按钮
 */
class BaseButton
{

    const TYPE_PAGE    = 'page';    // 打开弹窗页面
    const TYPE_REQUEST = 'request'; // 进行请求

    protected $title;


    protected $url;


    protected $type;


    protected $pageClass;

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

        if (Str::contains($class, 'J_iframe')) {
            $this->type = self::TYPE_PAGE;
        }
        else {
            $this->type = self::TYPE_REQUEST;
        }

        $this->attribute['class'] = $class;
    }

    /**
     * Render CreateButton.
     *
     * @return string
     */
    public function render(): string
    {
        return ' ' . Html::link($this->url, $this->title, $this->attribute, null, false) . ' ';
    }

    public function renderSkeleton(): array
    {
        $icon = '';
        if (preg_match('/class="(.*?)"/', $this->title, $matches)) {
            $icon = $matches[1];
        }

        return [
            'icon'  => $icon,
            'title' => trim(strip_tags($this->title)),
            'url'   => $this->url,
            'type'  => Str::contains($this->attribute['class'] ?? '', 'J_iframe') ? 'dialog' : '',
        ];
    }
}
