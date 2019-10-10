<?php namespace Poppy\Framework\Http\Pagination;

/**
 * 分页信息
 */
class PageInfo
{
	/**
	 * @var int 页码
	 */
	private $page;

	/**
	 * @var int 每页的分页数
	 */
	private $size;

	/**
	 * 分页构造器
	 * @param array $page_info 分页信息
	 */
	public function __construct($page_info)
	{
		$sizeConfig = abs(config('poppy.pagesize')) ?: 20;
		$page       = abs($page_info['page'] ?? 1);
		$size       = abs($page_info['size'] ?? $sizeConfig);
		$this->page = $page ?: 1;
		$this->size = $size ?: $sizeConfig;
	}

	/**
	 * 分页大小
	 * @return int
	 */
	public function size(): int
	{
		return $this->size;
	}

	/**
	 * 页码
	 * @return int
	 */
	public function page(): int
	{
		return $this->page;
	}
}