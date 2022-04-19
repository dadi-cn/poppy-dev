<?php

namespace Demo\Hooks\Demo;

use Poppy\Core\Services\Contracts\ServiceHtml;

/**
 * 输出 HTML
 */
class HtmlDemo implements ServiceHtml
{

    public function output()
    {
        return "<div></div>";
    }
}