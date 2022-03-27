<?php

namespace Demo\Http\Request\Web;

use Demo\Forms\FormEntrance;
use Demo\Models\DemoWebapp;
use Poppy\MgrPage\Classes\Form;
use Poppy\MgrPage\Classes\Layout\Content;
use Poppy\System\Http\Request\Web\WebController;

/**
 * 内容生成器
 */
class ContentController extends WebController
{

    /**
     * 主页
     */
    public function index()
    {
        $form    = new Form(new DemoWebapp());
        $content = new Content();
        $content->title('表单示例')
            ->description('这里列出了所有表单的可能性的选项')
            ->body($form);
        return $content;
    }


    public function form()
    {
        $content = new Content();
        $content->title('表单示例')
            ->description('这里列出了所有表单的可能性的选项')
            ->body(new FormEntrance());
        return $content;
    }
}