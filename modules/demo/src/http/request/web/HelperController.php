<?php

namespace Demo\Http\Request\Web;

use Demo\Forms\FormEntrance;
use Demo\Http\Forms\Helpers\FormEnvHelper;
use Demo\Http\Forms\Helpers\FormImageHelper;
use Demo\Http\Forms\Helpers\FormTreeHelper;
use Poppy\Framework\Helper\ImgHelper;
use Poppy\MgrPage\Classes\Layout\Content;
use Poppy\System\Http\Request\Web\WebController;

/**
 * 内容生成器
 */
class HelperController extends WebController
{

    /**
     * 主页
     */
    public function env()
    {
        return (new FormEnvHelper())->render();
    }

    public function image()
    {
        return (new FormImageHelper())->render();
    }

    public function tree()
    {
        return (new FormTreeHelper())->render();
    }


    public function imgStr()
    {
        ImgHelper::buildStr('Qianqian Li');
    }

    public function imgBmp()
    {
        $gd = imagecreatefrombmp(poppy_path('demo', 'tests/files/bear.bmp'));
        header("Content-type:image/jpg");
        imagepng($gd);
        imagedestroy($gd);
    }

    public function form(): Content
    {
        $content = new Content();
        $content->title('表单示例')
            ->description('这里列出了所有表单的可能性的选项')
            ->body(new FormEntrance());
        return $content;
    }
}