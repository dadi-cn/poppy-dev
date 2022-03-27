<?php

namespace Poppy\MgrPage\Classes\Widgets;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Poppy\Framework\Classes\Resp;
use Poppy\MgrPage\Classes\Layout\Content;
use function view;

class TableWidget extends Widget implements Renderable
{
    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var array
     */
    private $rows = [];

    /**
     * @var bool
     */
    private $withContent;

    /**
     * Table constructor.
     *
     * @param array $headers
     * @param array $rows
     */
    public function __construct(array $headers = [], array $rows = [])
    {
        parent::__construct();
        $this->setHeaders($headers);
        $this->setRows($rows);
        $this->withContent = true;
    }

    /**
     * Set table headers.
     *
     * @param array $headers
     *
     * @return $this
     */
    public function setHeaders(array $headers = []): self
    {
        $this->headers = $headers;

        return $this;
    }


    /**
     * 不使用容器显示
     * @return $this
     */
    public function withoutContent(): self
    {
        $this->withContent = false;
        return $this;
    }

    /**
     * Set table rows.
     *
     * @param array $rows
     *
     * @return $this
     */
    public function setRows(array $rows = []): self
    {
        if (Arr::isAssoc($rows)) {
            foreach ($rows as $key => $item) {
                $this->rows[] = [$key, $item];
            }

            return $this;
        }
        $this->rows = $rows;
        return $this;
    }

    /**
     * Render the table.
     * @throws \Throwable
     */
    public function render()
    {
        if ($this->isSkeleton()) {
            return Resp::success('获取数据成功', [
                'type'   => Widget::TYPE_STAITC_TABLE,
                'fields' => [
                    'headers' => $this->headers,
                    'rows'    => $this->rows,
                ],
            ]);
        }

        $content = view('py-mgr-page::tpl.widgets.table', [
            'headers'    => $this->headers,
            'rows'       => $this->rows,
        ])->render();
        if ($this->withContent) {
            return (new Content())->body($content);
        }
        else {
            return $content;
        }
    }
}
