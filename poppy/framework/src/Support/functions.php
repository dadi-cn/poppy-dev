<?php

use Illuminate\Container\Container;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Poppy\Faker\Factory;
use Poppy\Framework\Exceptions\FakerException;
use Poppy\Framework\Foundation\Application;
use Poppy\Framework\Foundation\Console\Kernel;
use Poppy\Framework\Helper\HtmlHelper;
use Poppy\MgrPage\Classes\Form\Field\Text;


if (!function_exists('route_url')) {
    /**
     * 自定义可以传值的路由写法
     * @param string            $route
     * @param array|string|null $route_params
     * @param array|null        $params
     * @param bool              $absolute 是否绝对路径
     * @return string
     */
    function route_url(string $route = '', $route_params = [], $params = null, $absolute = true): string
    {
        if (is_null($route_params)) {
            $route_params = [];
        }
        if ($route === '') {
            $route = Route::currentRouteName() ?? '';
            if (empty($route)) {
                return '';
            }
            $route_url = route($route, $route_params, $absolute);
        }
        elseif (strpos($route, '.') === false) {
            $route_url = url($route, $route_params);
        }
        else {
            $route_url = route($route, $route_params, $absolute);
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
    function command_exist($cmd): bool
    {
        try {
            $returnVal = shell_exec("which $cmd");

            return !empty($returnVal);
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
    function kv(array $desc, $key = null, $check_key = false)
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
     * @param string|null $name
     * @param string|null $default
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

        $result = Request::get($name, $default);

        // 字串移除空格
        if (is_string($result)) {
            return trim($result);
        }

        // 数组直接返回
        return $result ?: $default;
    }
}

if (!function_exists('is_post')) {
    /**
     * 当前访问方法是否是post请求
     * @return bool
     */
    function is_post(): bool
    {
        return Request::method() === 'POST';
    }
}

if (!function_exists('jwt_token')) {
    /**
     * 是否是 Jwt 请求
     * @param array $params 支持传入参数 [>3.1]
     * @return string
     */
    function jwt_token(array $params = []): string
    {
        $inputToken = (string) (Request::bearerToken() ?: input('token'));
        return $inputToken ?: ($params['token'] ?? '');
    }
}

if (!function_exists('post')) {
    /**
     * Identical function to input(), however restricted to $_POST values.
     * @param null $name
     * @param null $default
     * @return mixed
     * @deprecated
     * @see     input()
     * @removed 4.0
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
     * @deprecated
     * @see     Text
     * @removed 4.0
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
     * @param string|null $slug
     * @param string      $file
     * @return string
     */
    function poppy_path($slug = null, $file = ''): string
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
    function poppy_class(string $slug, $class = ''): string
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
     * @return bool
     */
    function is_production(): bool
    {
        return config('app.env') === 'production';
    }
}

if (!function_exists('home_path')) {
    /**
     * Poppy home path.
     * @param string $path
     * @return string
     */
    function home_path(string $path = ''): string
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
    function framework_path(string $path = ''): string
    {
        /** @var Application $container */
        $container = Container::getInstance();
        return $container->frameworkPath($path);
    }
}


if (!function_exists('py_container')) {
    /**
     * Get IoC Container.
     * @return Container | Application
     */
    function py_container(): Container
    {
        return Container::getInstance();
    }
}

if (!function_exists('py_console')) {
    /**
     * Get Console Container.
     * @return Kernel | ConsoleKernelContract
     */
    function py_console()
    {
        return app(ConsoleKernelContract::class);
    }
}


if (!function_exists('py_faker')) {
    /**
     * Get Console Container.
     * @return Faker\Generator
     * @throws FakerException
     */
    function py_faker(): Faker\Generator
    {
        if (class_exists('\Faker\Factory')) {
            return Factory::create('zh_CN');
        }
        else {
            throw new FakerException('未安装扩展 `poppy/faker`, 无法生成假数据');
        }
    }
}

if (!function_exists('parse_seo')) {
    /**
     * 解析 Seo 标题
     * 单参数 : 标题, 多参数, 标题, 描述
     * 数组参数 : 标题, 描述
     * @param mixed ...$args
     * @return array
     */
    function parse_seo(...$args): array
    {
        $title       = '';
        $description = '';
        if (func_num_args() === 1) {
            $arg = func_get_arg(0);
            if (is_array($arg)) {
                $title       = $arg['title'] ?? ($arg[0] ?? '');
                $description = $arg['description'] ?? ($arg[1] ?? '');
            }
            if (is_string(func_get_arg(0))) {
                $title       = $arg;
                $description = '';
            }
        }
        elseif (func_num_args() === 2) {
            $title       = func_get_arg(0);
            $description = func_get_arg(1);
        }
        return [$title, $description];
    }
}

if (!function_exists('x_app')) {
    /**
     * 获取 Header 中的 APP 信息
     * @param string $type version,id,os
     * @return string
     * @since      3.1
     * @deprecated 3.2 建议使用 x_header 替代 x_header('app-sign')
     * @see        x_header()
     */
    function x_app(string $type): string
    {
        /** @var \Illuminate\Http\Request $request */
        $request = app('request');
        $header  = $request->header('X-APP');
        if ($header) {
            $type = strtolower($type);
            try {
                $infos = json_decode($header, true);
            } catch (Throwable $e) {
                $infos = [];
            }
            return $infos[$type] ?? '';
        }

        $fullKey = strtoupper('x-app-' . $type);
        return $request->header($fullKey, '');
    }
}

if (!function_exists('x_header')) {
    /**
     * 获取 Header 中的 x-{ph} 信息, 不支持获取 x-app 里存储的 json 信息
     * 完整列表参考以下地址
     * @url https://wulicode.com/note/develop/standard/client.html
     * @param string $type ver,id,os
     * @return string
     * @since 3.2
     */
    function x_header(string $type): string
    {
        /** @var \Illuminate\Http\Request $request */
        $request = app('request');
        $fullKey = strtoupper('x-' . $type);
        return $request->header($fullKey, '');
    }
}