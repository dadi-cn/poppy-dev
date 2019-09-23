<?php namespace Poppy\Framework\Parse;

/*
	[UCenter] (C)2001-2099 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms
	$Id: xml.class.php 1059 2011-03-01 07:25:09Z monkey $

	$xml = Xml::encode(range('a', 'z'), false, 'game', 'some');
	Xml::decode($xml);
*/

/**
 * Xml Parser
 * $data = $xml->setNormal($is_normal)->parse($xml);
 */
class Xml
{
	/**
	 * parser
	 * @var resource $parser _parser
	 */
	private $parser;

	/**
	 * document
	 * @var mixed $document _document
	 */
	private $document;

	/**
	 * stack
	 * @var mixed $stack _stack
	 */
	private $stack;

	/**
	 * _data
	 * @var mixed $data _data
	 */
	private $data;

	/**
	 * _last_opened_tag
	 * @var mixed $lastOpenedTag _last_opened_tag
	 */
	private $lastOpenedTag;

	/**
	 * isNormal
	 * @var mixed $isNormal isNormal
	 */
	private $isNormal;

	/**
	 * _attrs
	 * @var array $attrs _attrs
	 */
	private $attrs = [];

	/**
	 * _failed
	 * @var bool $failed _failed
	 */
	private $failed = false;

	/**
	 * Xml constructor.
	 */
	public function __construct()
	{
		$this->parser = xml_parser_create('Utf-8');
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
		xml_set_object($this->parser, $this);
		xml_set_element_handler($this->parser, 'open', 'close');
		xml_set_character_data_handler($this->parser, 'data');
	}

	/**
	 * 释放指定的 XML 解析器
	 */
	public function destruct()
	{
		xml_parser_free($this->parser);
	}

	/**
	 * 解析
	 * @param string $data 要解析数据
	 * @return array|string
	 */
	public function parse(&$data)
	{
		$this->document = [];
		$this->stack    = [];

		return xml_parse($this->parser, $data, true) && !$this->failed ? $this->document : '';
	}

	/**
	 * 打开
	 * @param mixed $parser     parser
	 * @param mixed $tag        tag
	 * @param mixed $attributes attributes
	 */
	public function open(&$parser, $tag, $attributes)
	{
		$this->data   = '';
		$this->failed = false;
		if (!$this->isNormal) {
			if (isset($attributes['id']) && !is_string($this->document[$attributes['id']])) {
				$this->document = &$this->document[$attributes['id']];
			}
			else {
				$this->failed = true;
			}
		}
		else {
			if (!isset($this->document[$tag]) || !is_string($this->document[$tag])) {
				$this->document = &$this->document[$tag];
			}
			else {
				$this->failed = true;
			}
		}
		$this->stack[]       = &$this->document;
		$this->lastOpenedTag = $tag;
		$this->attrs         = $attributes;
	}

	/**
	 * 数据
	 * @param mixed  $parser parser
	 * @param string $data   data
	 */
	public function data(&$parser, $data)
	{
		if ($this->lastOpenedTag != null) {
			$this->data .= $data;
		}
	}

	/**
	 * 关闭
	 * @param mixed $parser parser
	 * @param mixed $tag    tag
	 */
	public function close(&$parser, $tag)
	{
		if ($this->lastOpenedTag == $tag) {
			$this->document      = $this->data;
			$this->lastOpenedTag = null;
		}
		array_pop($this->stack);
		if ($this->stack) {
			$this->document = &$this->stack[count($this->stack) - 1];
		}
	}

	/**
	 * 设置 normal
	 * @param mixed $is_normal is_normal
	 * @return $this
	 */
	public function setNormal($is_normal)
	{
		$this->isNormal = $is_normal;

		return $this;
	}

	/**
	 * 格式化
	 * @param array  $arr     arr
	 * @param bool   $html_on html_on
	 * @param string $root    root
	 * @param string $item    item
	 * @param int    $level   level
	 * @return string|string[]|null
	 */
	public function format($arr, $html_on = false, $root = 'root', $item = 'item', $level = 1)
	{
		$s     = $level == 1 ? "<?xml version=\"1.0\" encoding=\"Utf-8\"?>\r\n<{$root}>\r\n" : '';
		$space = str_repeat("\t", $level);
		foreach ($arr as $k => $v) {
			if (!is_array($v)) {
				$s .= $space . "<{$item} id=\"$k\">" . ($html_on ? '<![CDATA[' : '') . $v . ($html_on ? ']]>' : '') . "</{$item}>\r\n";
			}
			else {
				$s .= $space . "<{$item} id=\"$k\">\r\n" . $this->format($v, $html_on, $root, $item, $level + 1) . $space . "</{$item}>\r\n";
			}
		}
		$s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);

		return $level == 1 ? $s . "</{$root}>" : $s;
	}
}