<?php

namespace Poppy\MgrApp\Classes\Action;

/**
 * 复制自定义数据
 */
final class CopyAction extends Action
{

    /**
     * 可复制的内容
     * @var string
     */
    private string $content;

    public function __construct($title, $content)
    {
        parent::__construct($title, '');
        $this->icon    = 'document-copy';
        $this->content = $content;
    }

    /**
     * Action  渲染
     * @return array
     */
    public function struct(): array
    {
        return array_merge(parent::struct(), [
            'method'  => 'copy',
            'content' => $this->content
        ]);
    }
}
