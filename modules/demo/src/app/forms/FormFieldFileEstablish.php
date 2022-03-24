<?php

namespace Demo\App\Forms;

use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Widgets\FormWidget;

class FormFieldFileEstablish extends FormWidget
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
            'id'      => 5,
            'file'    => 'https://test-oss.iliexiang.com/_res/conf.d/web-laravel.conf',
            'audio'   => 'https://test-oss.iliexiang.com/_res/audio/actor.mp3',
            'video'   => 'https://test-oss.iliexiang.com/_res/video/h-918k.mp4',
            'image'   => 'https://test-oss.iliexiang.com/_res/images/png-59k.png',

            'file-code' => <<<CODE
\$this->file('file', 'File');
CODE,
            'audio-code' => <<<CODE
\$this->file('audio', 'Audio')->audio();
CODE,
            'video-code' => <<<CODE
\$this->file('video', 'Video')->video();
CODE,
            'pdf-code' => <<<CODE
\$this->file('pdf', 'Pdf')->extensions(['pdf']);
CODE,
            'image-code' => <<<CODE
\$this->image('image', 'Image');
CODE,
        ];
    }

    public function form()
    {
        $this->file('file', 'File');
        $this->code('file-code');
        $this->file('audio', 'Audio')->audio();
        $this->code('audio-code');
        $this->file('video', 'Video')->video();
        $this->code('video-code');
        $this->file('pdf', 'Pdf')->extensions(['pdf']);
        $this->code('pdf-code');
        $this->image('image', 'Image');
        $this->code('image-code');
    }
}
