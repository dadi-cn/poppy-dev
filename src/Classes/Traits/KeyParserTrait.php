<?php namespace Poppy\Framework\Classes\Traits;

/**
 * Key 解析器
 * 解析 key 字串到 namespace, group 和 item.
 * PS: namespace::group.item
 */
trait KeyParserTrait
{
	/**
	 * 解析的缓存存储
	 * 这里的命名方式, 因为是在 trait 中使用
	 * 所以这里的缓存的命名前面加上了 trait 的前缀 keyParser
	 * @var array $keyParserCache
	 */
	protected $keyParserCache = [];

	/**
	 * 根据 key 设置值
	 * @param string $key    key
	 * @param array  $parsed parsed
	 */
	public function setParsedKey($key, $parsed)
	{
		$this->keyParserCache[$key] = $parsed;
	}

	/**
	 * Check Key valid
	 * @param string $key key
	 * @return bool
	 */
	public function keyParserMatch($key)
	{
		if (preg_match('/[^0-9]?[a-z_\-0-9]{1,}::[^0-9]?[a-z_\/\-0-9]{1,}\.[^0-9]?[a-z\-0-9]{1,}/', $key)) {
			return true;
		}

		return false;
	}

	/**
	 * 解析 key 至 namespace, group, and item.
	 * @param string $key key
	 * @return array
	 */
	public function parseKey($key)
	{
		// 如果我们已经解析了给定的键，那么我们将返回已经拥有的缓存版本，因为这会节省我们一些处理
		// 我们缓存我们解析的每一个键，以便在以后的所有请求中快速返回
		if (isset($this->keyParserCache[$key])) {
			return $this->keyParserCache[$key];
		}

		$segments = explode('.', $key);

		// 如果密钥不包含双冒号，则意味着键不在名称空间中，而且只是一个常规配置项
		// 名称空间是为诸如模块之类的东西组织的配置项
		if (strpos($key, '::') === false) {
			$parsed = $this->keyParserParseBasicSegments($segments);
		}
		else {
			$parsed = $this->keyParserParseSegments($key);
		}

		// 一旦我们有了这个键的解析出的数组，比如它的组和命名空间，我们将在一个简单的列表中缓存每个数组
		// 这个列表中有键和解析数组，用于稍后请求的快速查找。
		return $this->keyParserCache[$key] = $parsed;
	}

	/**
	 * 解析数值中的基本片段并且返回可用的数组
	 * @param array $segments segments
	 * @return array
	 */
	protected function keyParserParseBasicSegments(array $segments)
	{
		// 基本数组中的第一个部分将永远是这个组，因此我们可以继续获取该部分
		// 如果只有一个完整的片段，我们只是把整个组从数组中拉出来，而不是一个单独的项。
		$group = $segments[0];

		if (count($segments) == 1) {
			return [null, $group, null];
		}
		// 如果这个组中有不止一个段，这意味着我们将从组中提取一个特定的项
		// 并且需要返回这个项目名称和组，这样我们就知道要从数组中提取哪些项了。

		$item = implode('.', array_slice($segments, 1));

		return [null, $group, $item];
	}

	/**
	 * 解析一系列命名空间的片段
	 * @param string $key key
	 * @return array
	 */
	protected function keyParserParseSegments($key)
	{
		[$namespace, $item] = explode('::', $key);

		// 我们将首先拆解第一个部分，以获得命名空间和组
		// 一旦我们有了这两部分数据，我们就可以继续解析这个项目的值
		$itemSegments = explode('.', $item);

		$groupAndItem = array_slice($this->keyParserParseBasicSegments($itemSegments), 1);

		return array_merge([$namespace], $groupAndItem);
	}
}