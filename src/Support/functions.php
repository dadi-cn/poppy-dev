<?php

use Illuminate\Container\Container;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Poppy\Framework\Foundation\Application;
use Poppy\Framework\Helper\HtmlHelper;

if (!function_exists('route_url')) {
	/**
	 * 自定义可以传值的路由写法
	 * @param       $route
	 * @param array $route_params
	 * @param array $params
	 * @return string
	 */
	function route_url($route = '', $route_params = [], $params = null)
	{
		if (is_null($route_params)) {
			$route_params = [];
		}
		if ($route === '') {
			$route = Route::currentRouteName() ?? '';
			if (empty($route)) {
				return '';
			}
			$route_url = route($route, $route_params);
		}
		elseif (strpos($route, '.') === false) {
			$route_url = url($route, $route_params);
		}
		else {
			$route_url = route($route, $route_params);
		}

		$route_url = trim($route_url, '?');
		if ($params) {
			return $route_url . '?' . (is_array($params) ? http_build_query($params) : $params);
		}

		return $route_url;
	}
}

if (!function_exists('route_prefix')) {
	/**
	 * 路由前缀
	 */
	function route_prefix()
	{
		$route = Route::currentRouteName();
		if (!$route) {
			return '';
		}

		return substr($route, 0, strpos($route, ':'));
	}
}

if (!function_exists('command_exist')) {
	/**
	 * 检测命令是否存在
	 * @param $cmd
	 * @return bool
	 */
	function command_exist($cmd)
	{
		try {
			$returnVal = shell_exec("which $cmd");

			return empty($returnVal) ? false : true;
		} catch (Exception $e) {
			return false;
		}
	}
}

if (!function_exists('kv')) {
	/**
	 * 返回定义的kv 值
	 * 一般用户模型中的数据返回
	 * @param array $desc
	 * @param null  $key
	 * @param bool  $check_key 检查key 是否正常
	 * @return array|string
	 */
	function kv($desc, $key = null, $check_key = false)
	{
		if ($check_key) {
			return isset($desc[$key]);
		}

		return !is_null($key)
			? $desc[$key] ?? ''
			: $desc;
	}
}

if (!function_exists('input')) {
	/**
	 * Returns an input parameter or the default value.
	 * Supports HTML Array names.
	 * <pre>
	 * $value = input('value', 'not found');
	 * $name = input('contact[name]');
	 * $name = input('contact[location][city]');
	 * </pre>
	 * Booleans are converted from strings
	 * @param string $name
	 * @param string $default
	 * @return string|array
	 */
	function input($name = null, $default = null)
	{
		if ($name === null) {
			return Request::all();
		}

		/*
		 * Array field name, eg: field[key][key2][key3]
		 */
		$name = implode('.', HtmlHelper::nameToArray($name));

		return Request::get($name, $default);
	}
}

if (!function_exists('is_post')) {
	/**
	 * 当前访问方法是否是post请求
	 * @return bool
	 */
	function is_post()
	{
		return Request::method() === 'POST';
	}
}

if (!function_exists('post')) {
	/**
	 * Identical function to input(), however restricted to $_POST values.
	 * @param null $name
	 * @param null $default
	 * @return mixed
	 */
	function post($name = null, $default = null)
	{
		if ($name === null) {
			return $_POST;
		}

		/*
		 * Array field name, eg: field[key][key2][key3]
		 */
		$name = implode('.', HtmlHelper::nameToArray($name));

		return Arr::get($_POST, $name, $default);
	}
}

if (!function_exists('get')) {
	/**
	 * Identical function to input(), however restricted to $_GET values.
	 * @param null $name
	 * @param null $default
	 * @return mixed
	 */
	function get($name = null, $default = null)
	{
		if ($name === null) {
			return $_GET;
		}

		/*
		 * Array field name, eg: field[key][key2][key3]
		 */
		$name = implode('.', HtmlHelper::nameToArray($name));

		return Arr::get($_GET, $name, $default);
	}
}

if (!function_exists('poppy_path')) {
	/**
	 * Return the path to the given module file.
	 * @param string $slug
	 * @param string $file
	 * @return string
	 */
	function poppy_path($slug = null, $file = '')
	{
		if (Str::contains($slug, 'poppy.')) {
			$modulesPath = app('path.poppy');
			$dir         = Str::after($slug, '.');
		}
		else {
			$modulesPath = app('path.module');
			$dir         = Str::after($slug, '.');
		}

		$filePath = $file ? '/' . ltrim($file, '/') : '';

		return $modulesPath . '/' . $dir . $filePath;
	}
}

if (!function_exists('poppy_class')) {
	/**
	 * Return the full path to the given module class or namespace.
	 * @param string $slug
	 * @param string $class
	 * @return string
	 */
	function poppy_class($slug, $class = '')
	{
		$module = app('poppy')->where('slug', $slug);

		if (is_null($module) || count($module) === 0) {
			return '';
		}

		$type       = Str::before($slug, '.');
		$moduleName = Str::after($slug, '.');
		$namespace  = Str::studly($moduleName);
		if ($type === 'poppy') {
			return $class ? "Poppy\\{$namespace}\\{$class}" : "Poppy\\{$namespace}";
		}

		return $class ? "{$namespace}\\{$class}" : $namespace;
	}
}

if (!function_exists('is_production')) {
	/**
	 * Check Env If Production
	 * @return string
	 */
	function is_production()
	{
		return env('APP_ENV', 'production') === 'production';
	}
}

if (!function_exists('home_path')) {
	/**
	 * Poppy home path.
	 * @param string $path
	 * @return string
	 */
	function home_path($path = '')
	{
		return app('path.poppy') . ($path ? DIRECTORY_SEPARATOR . $path : '');
	}
}

if (!function_exists('framework_path')) {
	/**
	 * Poppy framework path.
	 * @param string $path
	 * @return string
	 */
	function framework_path($path = '')
	{
		/** @var Application $container */
		$container = Container::getInstance();
		return $container->frameworkPath($path);
	}
}

