<?php namespace Poppy\Framework\Helper;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Throwable;

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
	 * @return string
	 */
	public static function correctName($name): string
	{
		return str_replace(
			[' ', '\\', '/', ':', '*', '?', '"', '<', '>', '|', "'", '$', '&', '%', '#', '@'],
			['-', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
			$name
		);
	}

	/**
	 * 获取json 对象或者数组
	 * @param string $filename filename
	 * @param bool   $is_array is_array
	 * @return array
	 */
	public static function getJson($filename, $is_array = true): array
	{
		try {
			$content = app('files')->get($filename);
		} catch (Throwable $e) {
			return [];
		}

		if (UtilHelper::isJson($content)) {
			return json_decode($content, $is_array);
		}

		return $is_array ? [] : json_decode(json_encode([]), true);
	}


	/**
	 * 文件路径
	 * @param string $path 路径
	 * @return string
	 */
	public static function dirPath($path): string
	{
		$path = str_replace('\\', '/', $path);
		if (substr($path, -1) !== '/') {
			$path .= '/';
		}

		return $path;
	}


	/**
	 * 获取目录大小
	 * @param string $directory 目录
	 * @param bool   $format    是否格式化输出
	 * @param int    $precision 百分比
	 * @return int|string
	 */
	public static function size($directory, $format = true, $precision = 2)
	{
		if (file_exists($directory) && is_dir($directory)) {
			$fileSize = 0;
			foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
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
	 * 设置目录下面的所有文件的访问和修改时间
	 * @param string $path 路径
	 * @return    bool    不是目录时返回false，否则返回 true
	 */
	public static function touch($path): bool
	{
		if (!is_dir($path)) {
			return false;
		}
		$path = self::dirPath($path);
		if (!is_dir($path)) {
			touch($path);
		}
		$files = glob($path . '*');
		foreach ($files as $v) {
			is_dir($v) ? self::touch($v) : touch($v);
		}

		return true;
	}

	/**
	 * 移除扩展名
	 * @param string $file file
	 * @return bool|string
	 */
	public static function removeExtension($file)
	{
		$ext = self::ext($file);

		return substr($file, 0, strlen($file) - (strlen($ext) + 1));
	}
}