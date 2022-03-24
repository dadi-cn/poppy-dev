<?php

namespace Demo\App\Forms;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Widgets\FormWidget;

class FormFieldImageEstablish extends FormWidget
{

    public function handle()
    {
        $message = print_r(input(), true);
        return Resp::success($message);
    }

    /**
     */
    public function data(): array
    {
        return [
            'id'          => 5,
            'default'     => 'default str',
            'image'       => 'https://wulicode.com/img/200x200/duoli',
            'image-v'     => 'https://wulicode.com/img/200x100/duoli',
            'multi-image' => [
                'https://wulicode.com/img/200x200/duoli',
                'https://wulicode.com/img/300x300/square',
                'https://wulicode.com/img/300x100/hengxiang',
                'https://wulicode.com/img/100x200/v',
                'https://wulicode.com/img/400x400/leecode',
            ],
            'multi-file'  => [
                'https://test-oss.iliexiang.com/_res/conf.d/web-laravel.conf',
                'https://test-oss.iliexiang.com/_res/audio/actor.mp3',
                'https://test-oss.iliexiang.com/_res/video/h-918k.mp4',
                'https://test-oss.iliexiang.com/_res/images/png-59k.png',
                'https://wulicode.com/img/200x100/duoli',
                'https://test-oss.iliexiang.com/_res/images/dynamic-quan.webp',
                'https://test-oss.iliexiang.com/_res/images/gif-268k.gif',
                'https://test-oss.iliexiang.com/_res/images/jpg-v-2m.jpg',
            ],

            'image-code'         => <<<CODE
\$this->image('image', '图片');
CODE,
            'image-v-code'       => <<<CODE
\$this->image('image-v', '图片:竖向');
CODE,
            'multi-image-4-code' => <<<CODE
\$this->multiImage('multi-image-4', '多图:Rule 4')->rules([
    Rule::max(4),
]);
CODE,
            'multi-file-code'    => <<<CODE
\$this->multiFile('multi-file', '多文件');
CODE,
            'multi-audio-code'   => <<<CODE
\$this->multiFile('multi-audio', 'Audios')->rules([
    Rule::max(4),
])->audio();
CODE,
            'multi-video-code'   => <<<CODE
\$this->multiFile('multi-video', 'Videos')->rules([
    Rule::max(4),
])->video();
CODE,
            'multi-image-code'   => <<<CODE
\$this->multiImage('multi-image', 'Images');
CODE,
        ];
    }

    public function form()
    {
        $this->image('image', '图片');
        $this->code('image-code');
        $this->image('image-v', '图片:竖向');
        $this->code('image-v-code');
        $this->multiImage('multi-image-4', '多图:Rule 4')->rules([
            Rule::max(4),
        ]);
        $this->multiFile('multi-file', '多文件');
        $this->code('multi-file-code');
        $this->multiFile('multi-audio', 'Audios')->rules([
            Rule::max(4),
        ])->audio();
        $this->code('multi-audio-code');
        $this->multiFile('multi-video', 'Videos')->rules([
            Rule::max(4),
        ])->video();
        $this->code('multi-video-code');
        $this->multiImage('multi-image', 'Images');
        $this->code('multi-image-code');

    }
}
