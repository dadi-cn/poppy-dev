<?php

namespace Poppy\Framework\Classes;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Poppy\Framework\Helper\StrHelper;
use Poppy\Framework\Helper\UtilHelper;
use Redirect;
use Request;
use Response;
use Session;
use TypeError;

/**
 * Resp
 */
class Resp
{
    /* 错误代码
     * ---------------------------------------- */
    public const SUCCESS       = 0;     // 正确
    public const ERROR         = 1;     // 错误
    public const TOKEN_MISS    = 2;     // 没有Token
    public const TOKEN_TIMEOUT = 3;     // Token 时间戳错误
    public const TOKEN_ERROR   = 4;     // Token 错误
    public const PARAM_ERROR   = 5;     // 参数错误
    public const SIGN_ERROR    = 6;     // 签名错误
    public const NO_AUTH       = 7;     // 无权操作
    public const INNER_ERROR   = 99;    // 其他错误

    /**
     * code
     * @var int $code
     */
    private int $code;

    /**
     * message
     * @var array|Translator|string|null $message
     */
    private $message = '操作出错了';

    /**
     * Resp constructor.
     * @param int    $code    code
     * @param string|MessageBag $message message
     */
    public function __construct(int $code, $message = '')
    {
        // init
        if (!$code) {
            $code = self::SUCCESS;
        }

        $this->code = $code;

        if (is_string($message) && !empty($message)) {
            $this->message = $message;
        }

        if ($message instanceof MessageBag) {
            $formatMessage = [];
            foreach ($message->all(':message') as $msg) {
                $formatMessage [] = $msg;
            }
            $this->message = $formatMessage;
        }

        if (!$message) {
            switch ($code) {
                case self::SUCCESS:
                    $message = (string) trans('poppy::resp.success');
                    break;
                case self::ERROR:
                    $message = (string) trans('poppy::resp.error');
                    break;
                case self::TOKEN_MISS:
                    $message = (string) trans('poppy::resp.token_miss');
                    break;
                case self::TOKEN_TIMEOUT:
                    $message = (string) trans('poppy::resp.token_timeout');
                    break;
                case self::TOKEN_ERROR:
                    $message = (string) trans('poppy::resp.token_error');
                    break;
                case self::PARAM_ERROR:
                    $message = (string) trans('poppy::resp.param_error');
                    break;
                case self::SIGN_ERROR:
                    $message = (string) trans('poppy::resp.sign_error');
                    break;
                case self::NO_AUTH:
                    $message = (string) trans('poppy::resp.no_auth');
                    break;
                case self::INNER_ERROR:
                default:
                    $message = (string) trans('poppy::resp.inner_error');
                    break;
            }
            $this->message = $message;
        }
    }

    /**
     * 返回错误代码
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * 返回错误信息
     * @return null|string
     */
    public function getMessage(): string
    {
        $env     = !is_production() ? '[' . config('app.env') . ']' : '';
        $message = (is_string($this->message) ? $this->message : implode(',', $this->message));
        if (Str::contains($message, $env)) {
            $message = str_replace($env, '.', $message);
        }

        return $env . $message;
    }

    /**
     * __toString
     * @return array|Translator|string|null
     */
    public function __toString()
    {
        if (is_array($this->message)) {
            return implode("\n", $this->message);
        }

        return $this->message;
    }

    /**
     * to array
     * @return array
     */
    public function toArray(): array
    {
        return [
            'status'  => $this->getCode(),
            'message' => $this->getMessage(),
        ];
    }

    /**
     * @param null|string $key Key
     * @return array|string
     */
    public static function desc(string $key = null)
    {
        $desc = [
            self::SUCCESS       => (string) trans('poppy::resp.success'),
            self::ERROR         => (string) trans('poppy::resp.error'),
            self::TOKEN_MISS    => (string) trans('poppy::resp.token_miss'),
            self::TOKEN_TIMEOUT => (string) trans('poppy::resp.token_timeout'),
            self::TOKEN_ERROR   => (string) trans('poppy::resp.token_error'),
            self::PARAM_ERROR   => (string) trans('poppy::resp.param_error'),
            self::SIGN_ERROR    => (string) trans('poppy::resp.sign_error'),
            self::NO_AUTH       => (string) trans('poppy::resp.no_auth'),
            self::INNER_ERROR   => (string) trans('poppy::resp.inner_error'),
        ];
        return kv($desc, $key);
    }

    /**
     * 错误输出
     * @param int                     $type   错误码
     * @param string|array|MessageBag $msg    类型
     * @param string|null|array       $append
     *                                        _json: 强制以 json 数据返回
     *                                        _location : 重定向
     *                                        _reload : 刷新页面, 需要提前设定 Session::previousUrl()
     *                                        _time   : 刷新或者重定向的时间(毫秒), 如果为null, 则显示页面信息, false 为立即刷新或者重定向, true 默认为 3S, 指定时间则为 xx ms
     * @param array|null              $input  表单提交的数据, 是否连带返回
     * @return JsonResponse|RedirectResponse
     */
    public static function web(int $type, $msg, $append = null, array $input = null)
    {
        if ($msg instanceof Exception || $msg instanceof TypeError) {
            $code    = $msg->getCode() ?: self::ERROR;
            $message = $msg->getMessage();
            $resp    = new self($code, $message);
        } elseif (!($msg instanceof self)) {
            $resp = new self($type, $msg);
        } else {
            $resp = $msg;
        }

        $arrAppend = StrHelper::parseKey($append);

        $isJson = false;
        // is json
        if (($arrAppend['_json'] ?? false) ||
            Request::ajax() ||
            Request::bearerToken() ||
            py_container()->isRunningIn('api')
        ) {
            $isJson = true;
            unset($arrAppend['_json']);
        }

        if ($isJson) {
            return self::webSplash($resp, !is_null($append) ? $arrAppend : null);
        }

        // is forgotten, 不写入 session 数据
        $location = $arrAppend['_location'] ?? '';
        $time     = $arrAppend['_time'] ?? true;

        if (isset($arrAppend['_reload'])) {
            $location = Session::previousUrl();
        }

        return self::webView($resp->getCode(), $resp->getMessage(), $time, $location, $input);
    }

    /**
     * data
     * @param int    $code    type
     * @param string $message msg
     * @return array
     * @deprecated 3.1
     * @removed    4.0
     */
    public static function data(int $code, string $message): array
    {
        if (!($message instanceof self)) {
            $resp = new self($code, $message);
        } else {
            $resp = $message;
        }

        return $resp->toArray();
    }

    /**
     * 返回成功输入
     * @param string|array|MessageBag $msg    提示消息
     * @param string|null|array       $append 追加的信息
     * @param array|null              $input  保留输入的数据
     * @return JsonResponse|RedirectResponse
     */
    public static function success($msg, $append = null, array $input = null)
    {
        return self::web(self::SUCCESS, $msg, $append, $input);
    }

    /**
     * 返回错误数组
     * @param string|array|MessageBag $msg    提示消息
     * @param string|null|array       $append 追加的信息
     * @param array|null              $input  保留输入的数据
     * @return JsonResponse|RedirectResponse
     */
    public static function error($msg, $append = null, array $input = null)
    {
        return self::web(self::ERROR, $msg, $append, $input);
    }

    /**
     * 返回自定义信息
     * @param int    $code    code
     * @param string $message message
     * @return array
     */
    public static function custom(int $code, string $message = ''): array
    {
        return (new self($code, $message))->toArray();
    }

    /**
     * 显示界面
     * @param int|bool|null $time     时间
     * @param string|null   $location location
     * @param array|null    $input    input
     * @return RedirectResponse|\Illuminate\Http\Response|Resp
     */
    private static function webView($code, $message, $time = null, string $location = null, array $input = null)
    {
        $messageTpl = config('poppy.framework.message_template');
        // default message template
        $view = 'poppy::template.message';
        if ($messageTpl) {
            foreach ($messageTpl as $context => $tplView) {
                if (py_container()->isRunningIn($context)) {
                    $view = $tplView;
                }
            }
        }

        // 立即
        if ($time === false) {
            $re = ($location !== 'back') ? Redirect::to($location) : Redirect::back();
            return $input ? $re->withInput($input) : $re;
        }

        // 采用页面
        $to = '';
        if (UtilHelper::isUrl($location)) {
            $to = $location;
        } elseif ($location === 'back') {
            $to = app('url')->previous();
        }

        // 默认 3s
        if ($time === true) {
            $time = 0;
        } else {
            $time = (int) $time;
        }

        if ($input) {
            Session::flashInput($input);
        }

        return response()->view($view, [
            'code'    => $code,
            'message' => $message,
            'to'      => $to,
            'input'   => $input,
            'time'    => $time,
        ]);
    }

    /**
     * 不支持 location
     * splash 不支持 location | back (Mark Zhao)
     * @param Resp         $resp   resp
     * @param string|array $append append
     * @return JsonResponse
     */
    private static function webSplash(Resp $resp, $append = ''): JsonResponse
    {
        $return = [
            'status'  => $resp->getCode(),
            'message' => $resp->getMessage(),
        ];

        $data = null;
        if (!is_null($append)) {
            if ($append instanceof Arrayable) {
                $data = $append->toArray();
            } elseif (is_string($append)) {
                $data = StrHelper::parseKey($append);
            } elseif (is_array($append)) {
                $data = $append;
            }
            $returnData = [];
            if (count($data)) {
                foreach ($data as $key => $current) {
                    $returnData[$key] = $current;
                }
            }
            $data = $returnData;
        }
        if (!is_null($data)) {
            $return['data'] = $data;
        }

        $format = config('poppy.framework.json_format', 0);
        return Response::json($return, 200, [], $format);
    }
}