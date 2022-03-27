<?php

namespace Poppy\SensitiveWord\Http\MgrApp;

use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Widgets\FormWidget;
use Poppy\SensitiveWord\Action\Word;
use function input;

class FormSensWordEstablish extends FormWidget
{

    protected string $title = '添加敏感词';

    public function handle()
    {
        $Word = new Word();
        if (!$Word->establish(input())) {
            return Resp::error($Word->getError());
        }
        return Resp::success('操作成功', 'motion|grid:reload');
    }

    public function form()
    {
        $this->textarea('word', '敏感词')->help('一行一个');
    }
}
