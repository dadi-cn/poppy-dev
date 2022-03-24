<?php

namespace Poppy\MgrApp\Classes\Widgets;

use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Contracts\Respable;

class SimpleTableWidget implements Respable
{

    private string $title = '';

    /**
     * @var array
     */
    private array $headers = [];

    /**
     * @var array
     */
    private array $rows = [];


    /**
     * Table constructor.
     *
     * @param array $headers
     * @param array $rows
     */
    public function __construct(array $headers = [], array $rows = [])
    {
        $this->setHeaders($headers);
        $this->setRows($rows);
    }

    /**
     * 设置数据表行
     * @param array $rows
     */
    public function setRows(array $rows = [])
    {
        foreach ($rows as $item) {
            $new = [];
            foreach ($item as $key => $it) {
                $new['k' . $key] = $it;
            }
            $this->rows[] = $new;
        }
    }

    /**
     * 设置标题
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Render the table.
     */
    public function resp()
    {
        return Resp::success('Struct', [
            'type'    => 'table',
            'title'   => $this->title,
            'headers' => $this->headers,
            'rows'    => $this->rows,
        ]);
    }

    /**
     * 设置 Header
     * @param array $headers
     */
    private function setHeaders(array $headers = [])
    {
        $new = [];
        foreach ($headers as $key => $header) {
            $new['k' . $key] = $header;
        }
        $this->headers = $new;
    }
}
