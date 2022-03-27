<?php

namespace Poppy\MgrPage\Classes\Widgets;

use Illuminate\Contracts\Support\Renderable;
use Throwable;
use function view;

class BoxWidget extends Widget implements Renderable
{

	/**
	 * @var string
	 */
	protected $title = '';

	/**
	 * @var string
	 */
	protected $content = 'here is the box content.';

	/**
	 * @var string
	 */
	protected $footer = '';

	/**
	 * @var array
	 */
	protected $tools = [];

	/**
	 * @var string
	 */
	protected $script;

	/**
	 * Box constructor.
	 *
	 * @param string $title
	 * @param string $content
	 */
	public function __construct($title = '', $content = '')
	{
		parent::__construct();
		if ($title) {
			$this->title($title);
		}

		if ($content) {
			$this->content($content);
		}
	}

	/**
	 * Set box content.
	 *
	 * @param string $content
	 *
	 * @return $this
	 */
	public function content(string $content): self
	{
		if ($content instanceof Renderable) {
			$this->content = $content->render();
		}
		else {
			$this->content = (string) $content;
		}

		return $this;
	}

	/**
	 * Set box title.
	 *
	 * @param string $title
	 *
	 * @return $this
	 */
	public function title(string $title): self
	{
		$this->title = $title;

		return $this;
	}

    /**
     * Render box.
     * @throws Throwable
     */
	public function render()
	{
		return view('py-mgr-page::tpl.widgets.box', $this->variables())->render();
	}

	/**
	 * 右上角工具栏
	 * @param $tools
	 * @return $this
	 */
	public function tools($tools = [])
	{
		$this->tools = array_merge($this->tools, $tools);

		return $this;
	}

	/**
	 * Variables in view.
	 *
	 * @return array
	 */
	protected function variables(): array
	{
		return [
			'title'   => $this->title,
			'content' => $this->content,
			'tools'   => $this->tools,
		];
	}
}
