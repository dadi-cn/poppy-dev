<?php namespace Poppy\Framework\Helper;

/*
 *
 * @package    system
 * @author     Mark
 * @copyright  Copyright (c) 2013 ixdcw team
 */

/**
 * 文件处理函数
 */
class FileHelper
{
	/**
	 * 获取文件的扩展名
	 * @param string $filename 文件名
	 * @return string   获取文件名扩展
	 */
	public static function ext($filename): string
	{
		return strtolower(trim(substr(strrchr($filename, '.'), 1)));
	}

	/**
	 * 返回文件纠正的名称, 替换掉特殊字符
	 * 返回合法的文件名
	 * @param string $name 可能不合法的文件名称
	 * @return mixed
	 */
	public static function vname($name)
	{
		return str_replace(
			[' ', '\\', '/', ':', '*', '?', '"', '<', '>', '|', "'", '$', '&', '%', '#', '@'],
			['-', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
			$name
		);
	}

	/**
	 * 文件下载
	 * @param        $file
	 * @param string $filename
	 * @param string $data
	 */
	public static function down($file, $filename = '', $data = '')
	{
		if (!$data && !is_file($file)) {
			exit;
		}
		$filename = $filename ?: basename($file);
		$fileType = self::ext($filename);
		$fileSize = $data ? \strlen($data) : filesize($file);
		ob_end_clean();
		@set_time_limit(0);
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		}
		else {
			header('Pragma: no-cache');
		}
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Content-Encoding: none');
		header('Content-Length: ' . $fileSize);
		header('Content-Disposition: attachment; filename=' . $filename);
		header('Content-Type: ' . $fileType);
		if ($data) {
			echo $data;
		}
		else {
			readfile($file);
		}
		exit;
	}

	/**
	 * 文件列表
	 * @param string $dir 文件夹名称
	 * @param array  $fs
	 * @return array
	 */
	public static function listAll($dir, array $fs = []): array
	{
		$files = glob($dir . '/*');
		if (!\is_array($files)) {
			return $fs;
		}
		foreach ($files as $file) {
			if (is_dir($file)) {
				$fs = self::listAll($file, $fs);
			}
			else {
				$fs[] = $file;
			}
		}

		return $fs;
	}

	/**
	 * 当前目录下的文件
	 * @param $dir
	 * @return array
	 */
	public static function subFile($dir): array
	{
		if (EnvHelper::isWindows()) {
			$dir = StrHelper::convert($dir, 'utf-8', 'gbk');
		}
		$dir    = self::path($dir, false);
		$files  = scandir($dir, SCANDIR_SORT_NONE);
		$return = [];
		if (!\is_array($files)) {
			return $return;
		}
		foreach ($files as $file) {
			if (strpos($file, '.') !== 0 && !is_dir($dir . '/' . $file)) {
				$return[] = $dir . '/' . $file;
			}
		}
		if (EnvHelper::isWindows()) {
			$return = StrHelper::batchConvert($return, 'gbk', 'utf-8');
		}

		return $return;
	}

	/**
	 * 列出所有文件夹, 忽略以 . 开头的文件夹(.git), 也就是忽略上级目录(.)和当前目录(..)
	 * @param $dir
	 * @return array
	 */
	public static function listDir($dir): array
	{
		if (EnvHelper::isWindows()) {
			$dir = StrHelper::batchConvert($dir, 'utf-8', 'gbk');
		}
		$dir    = self::path($dir, false);
		$subDir = self::subDir($dir);
		$dirs   = $subDir;
		if (!\is_array($dirs)) {
			return $dirs;
		}
		foreach ($subDir as $file) {
			$childDir = self::listDir($file);
			$dirs     = array_merge($dirs, $childDir);
		}
		if (EnvHelper::isWindows()) {
			$dirs = StrHelper::batchConvert($dirs, 'gbk', 'utf-8');
		}

		return $dirs;
	}

	/**
	 * 向文件中追加数据
	 * @param $file
	 * @param $str
	 */
	public static function append($file, $str)
	{
		$fh = fopen($file, 'ab');
		flock($fh, LOCK_EX);
		fwrite($fh, $str . PHP_EOL);
		fclose($fh);
	}

	/**
	 * 获取子目录
	 * @param $dir
	 * @return array
	 */
	public static function subDir($dir): array
	{
		if (EnvHelper::isWindows()) {
			$dir = StrHelper::convert($dir, 'utf-8', 'gbk');
		}
		$files   = scandir($dir, SCANDIR_SORT_NONE);
		$folders = [];
		if (!\is_array($files)) {
			return $folders;
		}
		foreach ($files as $file) {
			if (strpos($file, '.') === 0 || !is_dir($dir . '/' . $file)) {
				continue;
			}
			$folders[] = $dir . '/' . $file;
		}
		if (EnvHelper::isWindows()) {
			$folders = StrHelper::batchConvert($folders, 'gbk', 'utf-8');
		}

		return $folders;
	}

	/**
	 * 创建文件成功返回实际写入的数据块数目 失败返回false
	 * @param string $filename 文件名称
	 * @param string $data     数据
	 * @return bool|int     成功返回实际写入的数据块数目 失败返回false
	 */
	public static function put($filename, $data)
	{
		self::mkdir(\dirname($filename));
		if (@$fp = fopen($filename, 'wb')) {
			flock($fp, LOCK_EX);
			$len = fwrite($fp, $data);
			flock($fp, LOCK_UN);
			fclose($fp);

			return $len;
		}

		return false;
	}

	/**
	 * 使用 url 方式来获取内容
	 * @param string $url
	 * @param string $local
	 * @param string $refer
	 * @return bool|int
	 */
	public static function curlGet($url, $local, $refer = '')
	{
		if (!$refer) {
			$u   = parse_url($url);
			$ref = $u['scheme'] . '://' . $u['host'];
		}
		else {
			$ref = $refer;
		}

		$ch = curl_init();
		try {
			$falseIP = random_int(1, 255) . '.' . random_int(1, 255) . '.' . random_int(1, 255) . '.' . random_int(1, 255);
		} catch (\Throwable $e) {
			$falseIP = '111.111.111.111';
		}

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-FORWARDED-FOR:' . $falseIP, 'CLIENT-IP:' . $falseIP]);
		curl_setopt($ch, CURLOPT_REFERER, $ref);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$content = curl_exec($ch);
		$path    = preg_replace("/[\w]*_.*.jpg/i", '', $local);
		$path    = substr($path, 0, -1);
		if (!file_exists($path) && !mkdir($path, 0777, true) && !is_dir($path)) {
			throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
		}
		touch($local);

		return file_put_contents($local, $content);
	}

	/**
	 * 获取缓存文件内容
	 * @param string $filename
	 * @return string
	 */
	public static function get($filename)
	{
		if (EnvHelper::isWindows() && StrHelper::isUtf8($filename)) {
			$filename = StrHelper::convert($filename, 'utf-8', 'gbk');
		}

		return @file_get_contents($filename);
	}

	/**
	 * 获取json 对象或者数组
	 * @param      $filename
	 * @param bool $is_array
	 * @return mixed
	 */
	public static function getJson($filename, $is_array = true)
	{
		$content = self::get($filename);
		if (UtilHelper::isJson($content)) {
			return json_decode($content, $is_array);
		}

		return $is_array ? [] : json_decode(json_encode([]));
	}

	/**
	 * 读取文件数组
	 * @param $filename
	 * @return mixed|string|array
	 */
	public static function readPhp($filename)
	{
		if (file_exists($filename)) {
			return include $filename;
		}

		return '';
	}

	/**
	 * @param $filename
	 * @return bool         删除指定的文件名称
	 */
	public static function del($filename)
	{
		return is_file($filename) ? @unlink($filename) : false;
	}

	/**
	 * 文件路径
	 * @param $dirpath
	 * @return mixed|string
	 */
	public static function dirPath($dirpath)
	{
		$dirpath                                  = str_replace('\\', '/', $dirpath);
		if (substr($dirpath, -1) != '/') $dirpath = $dirpath . '/';

		return $dirpath;
	}

	/**
	 * 获取文件
	 * @param        $dir
	 * @param string $ext
	 * @param array  $fs
	 * @return array
	 */
	public static function lists($dir, $ext = '', $fs = [])
	{
		$files = glob($dir . '/*');
		if (!is_array($files)) return $fs;
		foreach ($files as $file) {
			if (is_dir($file)) {
				if (is_file($file . '/index.php') && is_file($file . '/config.inc.php')) continue;
				$fs = self::lists($file, $ext, $fs);
			}
			else {
				if ($ext) {
					if (preg_match("/\.($ext)$/i", $file)) $fs[] = $file;
				}
				else {
					$fs[] = $file;
				}
			}
		}

		return $fs;
	}

	/**
	 * 路径建立
	 * @param $path
	 * @return bool
	 */
	public static function mkdir($path)
	{
		if (is_dir($path)) return true;
		@mkdir($path, 0777, true);

		return is_dir($path);
	}

	/**
	 * 转换路径模式
	 * @param        $dir
	 * @param string $mode
	 * @param int    $require
	 */
	public static function dirChmod($dir, $mode = '', $require = 0)
	{
		if (!$require) $require = substr($dir, -1) == '*' ? 2 : 0;
		if ($require) {
			if ($require == 2) $dir = substr($dir, 0, -1);
			$dir                    = self::dirPath($dir);
			$list                   = glob($dir . '*');
			foreach ($list as $v) {
				if (is_dir($v)) {
					self::dirChmod($v, $mode, 1);
				}
				else {
					@chmod(basename($v), $mode);
				}
			}
		}
		if (is_dir($dir)) {
			@chmod($dir, $mode);
		}
		else {
			@chmod(basename($dir), $mode);
		}
	}

	/**
	 * 目录复制
	 * @param        $from_dir
	 * @param        $to_dir
	 * @param string $extension
	 * @return bool
	 * @throws \Exception
	 */
	public static function dirCopy($from_dir, $to_dir, $extension = '')
	{
		$from_dir = self::dirPath($from_dir);
		$to_dir   = self::dirPath($to_dir);
		if (!is_dir($from_dir)) return false;
		if (!is_dir($to_dir)) self::mkdir($to_dir);
		$list = glob($from_dir . '*');
		foreach ($list as $v) {
			$path = $to_dir . basename($v);
			if (is_file($path) && !is_writable($path)) {
				throw new \Exception($path . ' not writable');
			}

			if (is_dir($v)) {
				self::dirCopy($v, $path, $extension);
			}
			else {
				$extensions = explode(',', $extension);
				if (!$extension || in_array(pathinfo($v, PATHINFO_EXTENSION), $extensions)) {
					@copy($v, $path);
				}
			}
		}

		return true;
	}

	/**
	 * 目录删除
	 * @param $dir
	 * @return bool
	 */
	public static function dirDelete($dir)
	{
		$dir = self::dirPath($dir);
		if (!is_dir($dir)) return false;

		return @rmdir($dir);
	}

	/**
	 * 获取目录大小
	 * @param      $directory
	 * @param bool $format
	 * @param int  $precision
	 * @return int|string
	 */
	public static function size($directory, $format = true, $precision = 2)
	{
		if (file_exists($directory) && is_dir($directory)) {
			$fileSize = 0;
			foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file) {
				$fileSize += $file->getSize();
			}
		}
		else {
			return '0B';
		}
		if ($format) {
			return UtilHelper::formatBytes($fileSize, $precision);
		}

		return $fileSize;
	}

	/**
	 * 创建文件夹
	 * @param $path
	 * @return bool
	 */
	public static function create($path)
	{
		if (is_dir($path)) return true;

		return mkdir($path, 0777, true);
	}

	/**
	 * 转换路径模式
	 * @param        $dir
	 * @param string $mode
	 * @param int    $require
	 */
	public static function chmod($dir, $mode = '', $require = 0)
	{
		if (!$require) $require = substr($dir, -1) == '*' ? 2 : 0;
		if ($require) {
			if ($require == 2) $dir = substr($dir, 0, -1);
			$dir                    = self::path($dir);
			$list                   = glob($dir . '*');
			foreach ($list as $v) {
				if (is_dir($v)) {
					self::chmod($v, $mode, 1);
				}
				else {
					@chmod(basename($v), $mode);
				}
			}
		}
		if (is_dir($dir)) {
			@chmod($dir, $mode);
		}
		else {
			@chmod(basename($dir), $mode);
		}
	}

	/**
	 * 目录删除
	 * @param $dir
	 * @return bool
	 */
	public static function delete($dir)
	{
		$dir = self::path($dir);
		if (!is_dir($dir)) return false;
		if (substr($dir, 0, 1) == '.') die("Cannot Remove System DIR $dir ");
		$list = glob($dir . '*');
		if ($list) {
			foreach ($list as $v) {
				is_dir($v) ? self::create($v) : @unlink($v);
			}
		}

		return @rmdir($dir);
	}

	/**
	 * 文件路径, 补齐路径, unix 风格
	 * @param      $dir_path
	 * @param bool $suffix
	 * @return mixed|string
	 * @from phpcms v9 dir.func.php
	 */
	public static function path($dir_path, $suffix = true)
	{
		$dir_path                                   = str_replace(['\\', '//'], '/', $dir_path);
		if (substr($dir_path, -1) != '/') $dir_path = $dir_path . '/';
		if (!$suffix) {
			$dir_path = rtrim($dir_path, '/');
		}

		return $dir_path;
	}

	/**
	 * 转换目录下面的所有文件编码格式
	 * @param    string $in_charset  原字符集
	 * @param    string $out_charset 目标字符集
	 * @param    string $dir         目录地址
	 * @param    string $fileexts    转换的文件格式
	 * @return    string    如果原字符集和目标字符集相同则返回false，否则为true
	 */
	public static function convert($in_charset, $out_charset, $dir, $fileexts = 'php|html|htm|shtml|shtm|js|txt|xml')
	{
		if ($in_charset == $out_charset) return false;
		$list = self::listFile($dir);
		foreach ($list as $v) {
			if (pathinfo($v, PATHINFO_EXTENSION) == $fileexts && is_file($v)) {
				file_put_contents($v, iconv($in_charset, $out_charset, file_get_contents($v)));
			}
		}

		return true;
	}

	/**
	 * 列出目录下所有文件
	 * @param    string $path      路径
	 * @param    string $extension 扩展名
	 * @param    array  $list      增加的文件列表
	 * @return    array    所有满足条件的文件
	 */
	public static function listFile($path, $extension = '', $list = [])
	{
		$path  = self::path($path);
		$files = glob($path . '*');
		foreach ($files as $v) {
			$extensions = explode(',', $extension);
			if (!$extension || in_array(pathinfo($v, PATHINFO_EXTENSION), $extensions)) {
				$list[] = $v;
			}

			if (is_dir($v)) {
				$list = self::listFile($v, $extension, $list);
			}
		}

		return $list;
	}

	/**
	 * 设置目录下面的所有文件的访问和修改时间
	 * @param    string $path  路径
	 * @param    int    $mtime 修改时间
	 * @param    int    $atime 访问时间
	 * @return    array    不是目录时返回false，否则返回 true
	 */
	public static function touch($path, $mtime = 0, $atime = 0)
	{
		if (!$mtime) $mtime = time();
		if (!$atime) $atime = time();
		if (!is_dir($path)) return false;
		$path = self::path($path);
		if (!is_dir($path)) touch($path, $mtime, $atime);
		$files = glob($path . '*');
		foreach ($files as $v) {
			is_dir($v) ? self::touch($v, $mtime, $atime) : touch($v, $mtime, $atime);
		}

		return true;
	}

	/**
	 * 目录列表
	 * @param    string $dir       路径
	 * @param    int    $parent_id 父id
	 * @param    array  $dirs      传入的目录
	 * @return    array    返回目录列表
	 */
	public static function tree($dir, $parent_id = 0, $dirs = [])
	{
		global $id;
		if ($parent_id == 0) $id = 0;
		$list                    = glob($dir . '*');
		foreach ($list as $v) {
			if (is_dir($v)) {
				$id++;
				$dirs[$id] = ['id' => $id, 'parentid' => $parent_id, 'name' => basename($v), 'dir' => $v . '/'];
				$dirs      = self::tree($v . '/', $id, $dirs);
			}
		}

		return $dirs;
	}

	/**
	 * 列出当前文件夹下的目录
	 * @param      $DirPath
	 * @param bool $full
	 * @return array
	 */
	public static function listDirectory($DirPath, $full = false)
	{
		$dir_path = self::path($DirPath);
		$files    = scandir($dir_path);
		$return   = [];
		foreach ($files as $file) {
			if ($file != '.' && $file != '..') {
				$path = $dir_path . $file;
				if (is_dir($path)) {
					$return[] = $full ? self::path($path) : $file;
				}
			}
		}

		return $return;
	}

	/**
	 * 是否是目录
	 * @param      $directory
	 * @return bool
	 */
	public static function isDir($directory)
	{
		if (file_exists($directory) && is_dir($directory)) {
			return true;
		}

		return false;
	}

	/**
	 * 移除扩展名
	 * @param $file
	 * @return bool|string
	 */
	public static function removeExtension($file)
	{
		$ext = self::ext($file);

		return substr($file, 0, \strlen($file) - (\strlen($ext) + 1));
	}
}