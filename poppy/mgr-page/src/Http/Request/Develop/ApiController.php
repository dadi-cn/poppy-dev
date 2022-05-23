<?php

namespace Poppy\MgrPage\Http\Request\Develop;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;
use OviDigital\JsObjectToJson\JsConverter;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Helper\ArrayHelper;
use Poppy\Framework\Helper\StrHelper;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\System\Action\Apidoc;
use Poppy\System\Classes\Contracts\ApiSignContract;
use Psr\Http\Message\ResponseInterface;
use Session;
use Throwable;

/**
 * Api 文档控制器
 */
class ApiController extends DevelopController
{
    /**
     * @var array 菜单项, 用于右侧展示
     */
    protected $selfMenu;


    /**
     * @var Client
     */
    private Client $client;

    /**
     * 当前 Token
     * @var string
     */
    private $token;

    public function __construct()
    {
        parent::__construct();
        $apis = config('poppy.core.apidoc');

        if (count($apis)) {
            foreach ($apis as $k_cat => $v_cat) {
                if (isset($v_cat['title']) && $v_cat['title']) {
                    $this->selfMenu[$v_cat['title']] = config('app.url') . '/docs/' . $k_cat;
                }
            }
        }
        $this->selfMenu['apiDoc'] = 'https://apidocjs.com';
        \View::share([
            'self_menu' => $this->selfMenu,
        ]);
    }

    /**
     * 自动生成接口
     * @param string $type 支持的类型
     * @return Application|Factory|JsonResponse|RedirectResponse|Resp|Response|View
     */
    public function index($type = '')
    {
        $catalog = config('poppy.core.apidoc');
        if (!$catalog) {
            return Resp::error('尚未配置 apidoc 生成目录');
        }

        if (!$type) {
            $keys = array_keys($catalog);
            $type = $keys[0];
        }
        $definition = $catalog[$type];

        // 添加代签名
        $certificate = [];
        array_unshift($certificate, [
            'name'        => '_py_secret',
            'title'       => '代签名',
            'description' => '存在代签名字串之后可不用进行签名计算即可通过接口验证',
            'type'        => 'String',
        ]);
        $definition['sign_certificate'] = $certificate;

        $apiDocUrl = url('docs/' . $type);

        $this->seo('Restful-' . $type, '优雅的在线接口调试方案');

        $tokenGet = function ($key) use ($type) {
            if (Session::has($key)) {
                $token = Session::get($key);
                if (!Str::contains($token, '.')) {
                    return $token;
                }
                if (in_array($type, ['web', 'backend'])) {
                    // check token is valid
                    $this->token = $token;
                    try {
                        $item = $this->postWithSign(route('py-system:pam.auth.access'));
                    } catch (Exception $e) {
                        return $token;
                    }

                    if ($item->getStatusCode() === 200) {
                        $content = $item->getBody()->getContents();
                        $obj     = json_decode($content, true);
                        if ($obj['status'] === 0) {
                            \View::share('pam', $obj['data']);
                        }
                    }
                }
            }

            return Session::get($key);
        };

        $index   = input('url');
        $version = input('version', '1.0.0');
        $method  = input('method', 'get');
        try {
            $data      = $this->apiData($type, $index, $method, $version);
            $variables = [];
            if (isset($data['current_params'])) {
                foreach ($data['current_params'] as $current_param) {
                    if (!isset($data['params'][$current_param->field]) && !$current_param->optional) {
                        if (Str::startsWith($current_param->field, ':')) {
                            $variableName             = trim($current_param->field, ':');
                            $values                   = StrHelper::parseKey(strip_tags($current_param->description));
                            $variables[$variableName] = $values;
                        }
                        else {
                            $data['params'][$current_param->field] = $this->getParamValue($current_param);
                        }
                    }
                }
            }
            $data['version'] = 'v' . substr($version, 0, strpos($version, '.'));

            $key           = 'Success 200';
            $successFields = data_get($data['current'], 'success.fields');

            if ($successFields && $successFields->$key) {
                $success = $data['current']->success->fields->$key;
            }
            else {
                $success = [];
            }
            $data['token'] = $tokenGet('dev#' . $type . '#token');

            $headerSet       = function ($key) use ($type) {
                $headers = '';
                if (Session::has($key)) {
                    $headerStr = Session::get($key);
                    if (UtilHelper::isJson($headerStr)) {
                        $headers = $headerStr;
                    }
                    \View::share('headers', $headers);
                }

                return $headers;
            };
            $data['headers'] = $headerSet("dev#${type}#headers");

            // user
            $user  = [];
            $front = [];
            if (!isset($data['current'])) {
                return Resp::error('没有找到对应 URL 地址');
            }

            return view('py-mgr-page::develop.api.index', [
                'guard'      => $type,
                'data'       => $data,
                'variables'  => $variables,
                'success'    => $success,
                'definition' => $definition,
                'apidoc_url' => $apiDocUrl,
                'user'       => $user,
                'front'      => $front,
            ]);
        } catch (Throwable $e) {
            return Resp::error('Url : `' . $index . '` 存在错误 : ' . $e->getMessage());
        }
    }

    public function json($type)
    {
        $jsonFile = base_path('public/docs/' . $type . '/api_data.json');
        $content  = file_get_contents($jsonFile);
        return Resp::success('获取成功', [
            'content' => $content,
            '_json'   => true,
        ]);
    }

    /**
     * 设置
     * @param string $type  类型
     * @param string $field 字段
     * @return Application|Factory|JsonResponse|RedirectResponse|Resp|Response|View
     */
    public function field(string $type, string $field)
    {
        $sessionKey = 'dev#' . $type . '#' . $field;
        if (is_post()) {
            $value = input('value');
            if (!$value) {
                return Resp::error($field . '不能为空');
            }
            $value = StrHelper::trimSpace($value);
            Session::put($sessionKey, $value);
            return Resp::success('设置 ' . $field . ' 成功', '_top_reload|1');
        }
        $value = Session::get($sessionKey);
        return view('py-mgr-page::develop.api.field', compact('type', 'value', 'field'));
    }

    /**
     * api 登录
     * @return Application|Factory|JsonResponse|RedirectResponse|Resp|Response|View
     */
    public function login()
    {
        $type = input('guard');

        if (is_post()) {
            $input = array_merge(input(), [
                'device_id'   => uniqid('', true),
                'device_type' => 'webapp',
            ]);
            try {
                $resp = $this->postWithSign(route_url('py-system:pam.auth.login'), $input);
            } catch (Throwable $e) {
                return Resp::error($e->getMessage());
            }

            $content = $resp->getBody()->getContents();
            $data    = json_decode($content, true);
            if ((int) $data['status'] === Resp::SUCCESS) {
                $token = 'dev#' . $type . '#token';
                Session::put($token, data_get($data, 'data.token'));
            }
            else {
                return Resp::error($data['message']);
            }

            return Resp::success('登录成功', '_top_reload|1');
        }

        $headers = Session::get('dev#web#headers');

        return view('py-mgr-page::develop.api.login', compact('type', 'headers'));
    }

    /**
     * 获取生成的 api 数据
     * @param string $type    类型
     * @param null   $prefix  前缀
     * @param string $method  方法
     * @param string $version 版本
     * @return array
     */
    protected function apiData(string $type, $prefix = null, $method = 'get', $version = '1.0.0'): array
    {
        $catalog  = config('poppy.core.apidoc');
        $docs     = $catalog[$type];
        $Apidoc   = new Apidoc();
        $jsObject = $Apidoc->local($type);
        $jsonFile = base_path('public/docs/' . $type . '/index.html');
        $data     = [];
        if (file_exists($jsonFile)) {
            $data['file_exists'] = true;
            $data['url_base']    = config('app.url');
            $data['content']     = json_decode(str_replace(['"`"', '`'], '', JsConverter::convertToJson($jsObject)), false);
            $content             = new Collection($data['content']);
            $group               = $content->groupBy('groupTitle');
            // add 排序
            $group            = $group->map(function (\Illuminate\Support\Collection $group) {
                return $group->sortBy('title');
            });
            $data['group']    = $group;
            $data['versions'] = [];
            $url              = $prefix;
            if (!$url) {
                $url    = trim($docs['default_url'] ?? '', '/');
                $method = $docs['method'] ?? 'get';
            }
            if ($url) {
                foreach ($content as $val) {
                    $valUrl = trim($val->url, '/');
                    $url    = trim($url, '/');
                    if ($val->type === $method && $valUrl === $url && $val->version === $version) {
                        $data['index']   = $url;
                        $data['current'] = $val;
                        if (isset($data['current']->query)) {
                            $data['current_params'] = $data['current']->query;
                        }
                    }
                    if ($val->type === $method && $valUrl === $url) {
                        $vk                          = substr($val->version, 0, strpos($val->version, '.'));
                        $data['versions']['v' . $vk] = $val->version;
                    }
                }
            }
        }
        else {
            $data['file_exists'] = false;
        }
        if (isset($data['versions'])) {
            ksort($data['versions']);
        }

        return $data;
    }

    /**
     * 获取随机参数值
     * @param mixed $param 参数
     * @return int|string
     * @throws ApplicationException
     */
    private function getParamValue($param)
    {
        /*
        "group": "Parameter"
        "type": "<p>String</p> "
        "optional": false
        "field": "device_id"
        "size": "2..5"
        "description": "<p>设备ID, 设备唯一的序列号</p> "
         */
        if (!isset($param->type)) {
            throw new ApplicationException('参数 `' . data_get($param, 'field') . '` 未配置类型, 例如: {string}');
        }
        $type          = strtolower(strip_tags(trim($param->type)));
        $allowedValues = $param->allowedValues ?? [];
        $size          = $param->size ?? '';
        switch ($type) {
            case 'string':
                if (strpos($size, '..') !== false) {
                    [$start, $end] = explode('..', $size);
                    $start = (int) $start;
                    $end   = (int) $end;

                    $length = rand($start, $end);

                    return Str::random($length);
                }
                if ($allowedValues) {
                    shuffle($allowedValues);

                    return $allowedValues[0];
                }

                return '';
            case 'boolean':
                return rand(0, 1);
            case 'number':
                if (strpos($size, '-') !== false) {
                    [$start, $end] = explode('-', $size);
                    $start = (int) $start;
                    $end   = (int) $end;

                    return rand($start, $end);
                }
                if (strpos($size, '..') !== false) {
                    [$start, $end] = explode('..', $size);
                    $start = (int) $start;
                    $end   = (int) $end;

                    $start = ((int) str_pad(1, $start, 0));
                    $end   = ((int) str_pad(1, $end + 1, 0)) - 1;

                    return rand($start, $end);
                }
                if ($allowedValues) {
                    shuffle($allowedValues);

                    return $allowedValues[0];
                }

                return rand(0, 99999999);
        }

        return '';
    }

    /**
     * @param $url
     * @param $params
     * @return ResponseInterface
     * @throws GuzzleException
     */
    private function postWithSign($url, $params = []): ResponseInterface
    {
        /** @var ApiSignContract $sign */
        $sign   = app(ApiSignContract::class);
        $params = array_merge(ArrayHelper::mapNull(Arr::except($params, '_token')), [
            'timestamp' => Carbon::now()->timestamp,
        ]);

        $params['token'] = $this->token;
        $params['sign']  = $sign->sign($params);

        $headers = Session::get('dev#web#headers') ?? [];
        if ($headers && UtilHelper::isJson($headers)) {
            $headers = json_decode($headers, true);
        }

        $headers = array_merge([
            'Authorization' => 'Bearer ' . $this->token,
        ], $headers);

        $this->client = new Client();
        return $this->client->post($url, [
            'headers'     => $headers,
            'form_params' => $params,
        ]);
    }
}