<?php namespace Poppy\Framework\Helper;

/**
 * 通用的树型类，可以生成任何树型结构
 */
class TreeHelper
{
	/**
	 * 生成树型结构所需要的2维数组
	 * @var array
	 */
	public $arr  = [];

	public $tree = [];

	private $key_id;

	private $key_pid;

	private $key_title;

	/**
	 * 生成树型结构所需修饰符号，可以换成图片
	 * @var array
	 */
	public $icon  = ['&nbsp;│', '&nbsp;├', '&nbsp;└'];

	public $space = '&nbsp;';

	/**
	 * @access private
	 */
	public $ret = '';

	/**
	 * 构造函数，初始化类
	 * @param array  $arr 2维数组，例如：
	 *                    array(
	 *                    1 => array('id'=>'1','pid'=>0,'name'=>'一级栏目一'),
	 *                    2 => array('id'=>'2','pid'=>0,'name'=>'一级栏目二'),
	 *                    3 => array('id'=>'3','pid'=>1,'name'=>'二级栏目一'),
	 *                    4 => array('id'=>'4','pid'=>1,'name'=>'二级栏目二'),
	 *                    5 => array('id'=>'5','pid'=>2,'name'=>'二级栏目三'),
	 *                    6 => array('id'=>'6','pid'=>3,'name'=>'三级栏目一'),
	 *                    7 => array('id'=>'7','pid'=>3,'name'=>'三级栏目二')
	 *                    )
	 * @param string $k_id
	 * @param string $k_pid
	 * @param string $k_title
	 * @return bool
	 */
	public function init($arr = [], $k_id = 'id', $k_pid = 'pid', $k_title = 'name')
	{
		$this->arr       = $arr;
		$this->ret       = '';
		$this->key_id    = $k_id;
		$this->key_pid   = $k_pid;
		$this->key_title = $k_title;

		return is_array($arr);
	}

	public function replaceSpace()
	{
		$this->icon  = [' │', ' ├', ' └'];
		$this->space = ' ';
	}

	/**
	 * 得到父级数组
	 * @param int
	 * @return array|bool
	 */
	public function getParent($id)
	{
		$newArray = [];
		if (!isset($this->arr[$id])) {
			return false;
		}
		$pid = $this->arr[$id][$this->key_pid];
		$pid = $this->arr[$pid][$this->key_pid];
		if (is_array($this->arr)) {
			foreach ($this->arr as $kid => $a) {
				if ($a[$this->key_pid] == $pid) $newArray[$kid] = $a;
			}
		}

		return $newArray;
	}

	/**
	 * 得到子级数组
	 * @param int
	 * @return array
	 */
	public function getChild($id)
	{
		$newArray = [];
		if (is_array($this->arr)) {
			foreach ($this->arr as $kid => $a) {
				if ($a[$this->key_pid] == $id) $newArray[$kid] = $a;
			}
		}

		return $newArray ? $newArray : false;
	}

	/**
	 * 得到当前位置数组
	 * @param $id
	 * @param $newArray
	 * @return array|bool
	 */
	public function getPos($id, &$newArray)
	{
		$a = [];
		if (!isset($this->arr[$id])) return false;
		$newArray[] = $this->arr[$id];
		$pid        = $this->arr[$id][$this->key_pid];
		if (isset($this->arr[$pid])) {
			$this->getPos($pid, $newArray);
		}
		if (is_array($newArray)) {
			krsort($newArray);
			foreach ($newArray as $v) {
				$a[$v[$this->key_id]] = $v;
			}
		}

		return $a;
	}

	/**
	 * 得到树型结构
	 * @param int    $myid        ID，表示获得这个ID下的所有子级
	 * @param string $str         生成树型结构的基本代码，例如："<option value=\$id \$selected>\$spacer\$name</option>"
	 * @param int    $selected_id 被选中的ID，比如在做树型下拉框的时候需要用到
	 * @param string $adds        是否添加指示标志
	 * @param string $str_group
	 * @return string
	 */
	public function getTree($myid, $str, $selected_id = 0, $adds = '', $str_group = '')
	{
		$number   = 1;
		$children = $this->getChild($myid);
		if (is_array($children)) {
			$total = count($children);
			foreach ($children as $node_id => $node) {
				$j = $k = '';
				if ($number == $total) {
					$j .= $this->icon[2];
				}
				else {
					$j .= $this->icon[1];
					$k = $adds ? $this->icon[0] : '';
				}

				$spacer   = $adds ? $adds . $j : '';
				$selected = $node_id == $selected_id ? 'selected="selected"' : '';
				@extract($node);
				$nstr = '';
				if ($node[$this->key_pid] == 0 && isset($node['str_group'])) {
					eval("\$nstr = \"$str_group\";");
				}
				else {
					eval("\$nstr = \"$str\";");
				}
				$this->ret .= $nstr;
				$nbsp      = $this->space;
				$this->getTree($node_id, $str, $selected_id, $adds . $k . $nbsp, $str_group);
				$number++;
			}
		}

		return $this->ret;
	}

	public function getTreeArray($id, $adds = '', $type = 'default')
	{
		$number   = 1;
		$children = $this->getChild($id);
		if (is_array($children)) {
			$total = count($children);
			foreach ($children as $node_id => $node) {
				$j = $k = '';
				if ($number == $total) {
					$j .= $this->icon[2];
				}
				else {
					$j .= $this->icon[1];
					$k = $adds ? $this->icon[0] : '';
				}
				$spacer                           = $adds ? $adds . $j : '';
				$this->tree[$node[$this->key_id]] = $spacer . $node[$this->key_title];
				$nbsp                             = $this->space;
				$this->getTreeArray($node_id, $adds . $k . $nbsp);
				$number++;
			}
		}
		if ($type == 'default') {
			return $this->tree;
		}
		 
			$tree = [];
			foreach ($this->tree as $key => $value) {
				$tree[] = [
					'key'   => $key,
					'value' => $value,
				];
			}

			return $tree;
	}

	/**
	 * 同上一方法类似,但允许多选
	 * @param        $myid
	 * @param        $str
	 * @param int    $sid
	 * @param string $adds
	 * @return string
	 */
	public function getTreeMulti($myid, $str, $sid = 0, $adds = '')
	{
		$number = 1;
		$child  = $this->getChild($myid);
		if (is_array($child)) {
			$total = count($child);
			foreach ($child as $kid => $a) {
				$j = $k = '';
				if ($number == $total) {
					$j .= $this->icon[2];
				}
				else {
					$j .= $this->icon[1];
					$k = $adds ? $this->icon[0] : '';
				}
				$spacer = $adds ? $adds . $j : '';

				$selected = $this->_has($sid, $kid) ? 'selected' : '';
				@extract($a);
				$nstr = '';
				eval("\$nstr = \"$str\";");
				$this->ret .= $nstr;
				$this->getTreeMulti($kid, $str, $sid, $adds . $k . '&nbsp;');
				$number++;
			}
		}

		return $this->ret;
	}

	/**
	 * @param   int    $myid 要查询的ID
	 * @param   string $str  第一种HTML代码方式
	 * @param  string  $str2 第二种HTML代码方式
	 * @param int      $sid  默认选中
	 * @param string   $adds 前缀
	 * @return string
	 */
	public function getTreeCategory($myid, $str, $str2, $sid = 0, $adds = '')
	{
		$number = 1;
		$child  = $this->getChild($myid);
		if (is_array($child)) {
			$total = count($child);
			foreach ($child as $id => $a) {
				$j = $k = '';
				if ($number == $total) {
					$j .= $this->icon[2];
				}
				else {
					$j .= $this->icon[1];
					$k = $adds ? $this->icon[0] : '';
				}
				$spacer   = $adds ? $adds . $j : '';
				$selected = $this->_has($sid, $id) ? 'selected' : '';
				@extract($a);
				$nstr = '';
				if (empty($html_disabled)) {
					eval("\$nstr = \"$str\";");
				}
				else {
					eval("\$nstr = \"$str2\";");
				}
				$this->ret .= $nstr;
				$this->getTreeCategory($id, $str, $str2, $sid, $adds . $k . '&nbsp;');
				$number++;
			}
		}

		return $this->ret;
	}

	/**
	 * 同上一类方法，jquery treeview 风格，可伸缩样式（需要treeview插件支持）
	 * @param int    $myid         表示获得这个ID下的所有子级
	 * @param string $effected_id  需要生成treeview目录数的id
	 * @param string $str          末级样式
	 * @param string $str2         目录级别样式
	 * @param int    $showlevel    直接显示层级数，其余为异步显示，0为全部限制
	 * @param string $style        目录样式 默认 filetree 可增加其他样式如'filetree treeview-famfamfam'
	 * @param int    $currentlevel 计算当前层级，递归使用 适用改函数时不需要用该参数
	 * @param bool   $recursion    递归使用 外部调用时为FALSE
	 * @return string
	 */
	public function getTreeView(
		$myid,
		$effected_id = 'example',
		$str = '<span class="file">$name</span>',
		$str2 = '<span class="folder">$name</span>',
		$showlevel = 0,
		$style = 'filetree ',
		$currentlevel = 1,
		$recursion = false
	)
	{
		$child = $this->getChild($myid);
		if (!defined('EFFECTED_INIT')) {
			$effected = ' id="' . $effected_id . '"';
			define('EFFECTED_INIT', 1);
		}
		else {
			$effected = '';
		}
		$placeholder = '<ul><li><span class="placeholder"></span></li></ul>';
		if (!$recursion) $this->str .= '<ul' . $effected . '  class="' . $style . '">';
		foreach ($child as $id => $a) {
			@extract($a);
			if ($showlevel > 0 && $showlevel == $currentlevel && $this->getChild($id)) $folder = 'hasChildren'; //如设置显示层级模式@2011.07.01
			$floder_status                                                                     = isset($folder) ? ' class="' . $folder . '"' : '';
			$this->ret .= $recursion ? '<ul><li' . $floder_status . ' id=\'' . $id . '\'>' : '<li' . $floder_status . ' id=\'' . $id . '\'>';
			$recursion     = false;
			$nstr          = '';
			if ($this->getChild($id)) {
				eval("\$nstr = \"$str2\";");
				$this->ret .= $nstr;
				if ($showlevel == 0 || ($showlevel > 0 && $showlevel > $currentlevel)) {
					$this->getTreeView($id, $effected_id, $str, $str2, $showlevel, $style, $currentlevel + 1, true);
				}
				elseif ($showlevel > 0 && $showlevel == $currentlevel) {
					$this->ret .= $placeholder;
				}
			}
			else {
				eval("\$nstr = \"$str\";");
				$this->ret .= $nstr;
			}
			$this->ret .= $recursion ? '</li></ul>' : '</li>';
		}
		if (!$recursion) $this->ret .= '</ul>';

		return $this->ret;
	}

	private function _has($list, $item)
	{
		return strpos(',,' . $list . ',', ',' . $item . ',');
	}
}