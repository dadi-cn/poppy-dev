<?php

namespace Demo\Classes\Layout;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Poppy\Framework\Exceptions\PoppyException;
use Poppy\Framework\Helper\EnvHelper;
use Poppy\Framework\Helper\StrHelper;
use Poppy\MgrPage\Classes\Layout\Content;
use Poppy\MgrPage\Classes\Layout\Row;
use Throwable;

class Demo implements Renderable
{
    /**
     * Content title.
     *
     * @var string
     */
    protected $title = '';

    /**
     * Content description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * @var Row[]
     */
    protected $rows = [];

    /**
     * Content constructor.
     *
     * @param Closure|null $callback
     * @throws PoppyException
     */
    public function __construct(Closure $callback = null)
    {
        if (!app('poppy')->exists('poppy.mgr-page')) {
            throw new PoppyException('模块 `poppy.mgr-page` 不存在');
        }
        if ($callback instanceof Closure) {
            $callback($this);
        }
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function title(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set description of content.
     *
     * @param string $description
     *
     * @return $this
     */
    public function description($description = '')
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Alias of method row.
     *
     * @param mixed $content
     *
     * @return Content
     */
    public function body($content)
    {
        return $this->row($content);
    }

    /**
     * Add one row for content body.
     *
     * @param $content
     *
     * @return $this
     */
    public function row($content)
    {
        if ($content instanceof Closure) {
            $row = new Row();
            call_user_func($content, $row);
            $this->addRow($row);
        }
        else {
            $this->addRow(new Row($content));
        }

        return $this;
    }

    /**
     * Render giving view as content body.
     *
     * @param string $view
     * @param array  $data
     *
     * @return Content
     */
    public function view($view, $data)
    {
        return $this->body(view($view, $data));
    }

    /**
     * @param $var
     *
     * @return Content
     */
    public function dump($var)
    {
        return $this->row(dump(...func_get_args()));
    }

    /**
     * Build html of content.
     *
     * @return string
     */
    public function build()
    {
        ob_start();

        foreach ($this->rows as $row) {
            $row->build();
        }

        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;
    }

    /**
     * Render this content.
     * @throws Throwable
     */
    public function render()
    {
        $menus     = app('poppy.core.module')->menus()->withType('demo', []);
        $variables = [
            'title'       => $this->title,
            'description' => $this->description,
            'host'        => StrHelper::formatId(EnvHelper::host()) . '-demo',
            'content'     => $this->build(),
            '_menus'      => $menus,
        ];

        // 这里的显示依赖于 py-mgr-page
        // 因为只是后台, 所以提取一个给后边用
        return view('demo::backend.tpl.demo', $variables)->render();
    }

    /**
     * Add Row.
     *
     * @param Row $row
     */
    protected function addRow(Row $row)
    {
        $this->rows[] = $row;
    }
}
