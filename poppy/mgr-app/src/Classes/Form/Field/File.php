<?php

namespace Poppy\MgrApp\Classes\Form\Field;

use Poppy\MgrApp\Classes\Form\FormItem;
use function collect;

class File extends FormItem
{

    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->file();
    }

    public function audio(): self
    {
        $extensions = ['.mp3', '.m4a', '.wav', '.aac'];
        $this->setAttribute('accept', implode(',', $extensions));
        $this->setAttribute('type', 'audio');
        return $this;
    }

    public function video(): self
    {
        $extensions = ['.mp4', '.rm', '.rmvb', '.wmv'];
        $this->setAttribute('accept', implode(',', $extensions));
        $this->setAttribute('type', 'video');
        return $this;
    }

    public function extensions(array $extensions = [], $type = 'file'): self
    {
        $extensions = collect($extensions)->map(function ($ext) {
            return '.' . $ext;
        });
        $this->setAttribute('accept', implode(',', $extensions->toArray()));
        $this->setAttribute('type', 'file');
        return $this;
    }

    /**
     * @return $this
     */
    protected function image(): self
    {
        $extensions = ['.jpg', '.jpeg', '.png', '.gif'];
        $this->setAttribute('accept', implode(',', $extensions));
        $this->setAttribute('type', 'images');
        return $this;
    }

    private function file()
    {
        $extensions = ['.zip', '.rp', '.rplib', '.svga', '.xls', '.xlsx', '.doc', '.docx', '.ppt', '.pptx', '.pdf'];
        $this->setAttribute('accept', implode(',', $extensions));
        $this->setAttribute('type', 'file');
    }
}
