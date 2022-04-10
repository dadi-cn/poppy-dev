<?php

use Carbon\Carbon;
use Illuminate\Container\Container;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Poppy\Framework\Foundation\Application;
use Poppy\Framework\Helper\EnvHelper;
use Poppy\Framework\Helper\StrHelper;
use Poppy\Framework\Helper\TimeHelper;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\System\Classes\PySystemDef;
use Poppy\System\Models\PamAccount;

if (!function_exists('sys_setting')) {
    /**
     * Get System Setting
     * @param string $key
     * @param null   $default
     * @return mixed
     */
    function sys_setting(string $key, $default = null)
    {
        return app('poppy.system.setting')->get($key, $default);
    }
}

if (!function_exists('sys_gen_order')) {
    /**
     * 生成订单号
     * @param string $prefix
     * @return string
     */
    function sys_gen_order($prefix = ''): string
    {
        try {
            $sequence = random_int(1000, 9999);
        } catch (Exception $e) {
            $sequence = Str::random(4);
        }
        $current = Carbon::now()->format('YmdHis');

        return sprintf('%s%s%s%s', strtoupper($prefix), $current, TimeHelper::micro(), sprintf("%'.04d", $sequence));
    }
}

if (!function_exists('sys_order_prefix')) {
    /**
     * 生成订单号
     * @param string $order_no
     * @return string
     */
    function sys_order_prefix(string $order_no): string
    {
        if (preg_match('/^([a-zA-z]{1,})\d*/i', $order_no, $matches)) {
            return $matches[1];
        }

        return 'other';
    }
}

if (!function_exists('sys_trans')) {
    /**
     * translate line
     * @param string $line
     * @param array  $replace
     * @return string
     */
    function sys_trans(string $line, array $replace = []): string
    {
        foreach ($replace as $key => $value) {
            $line = str_replace(
                [':' . $key, ':' . Str::upper($key), ':' . Str::ucfirst($key)],
                [$value, Str::upper($value), Str::ucfirst($value)],
                $line
            );
        }

        return $line;
    }
}

if (!function_exists('sys_seo')) {
    function sys_seo(...$args)
    {
        [$title, $description] = parse_seo($args);
        $title       = $title ? $title . '-' . config('poppy.framework.title') : config('poppy.framework.title');
        $description = $description ?: config('poppy.framework.description');
        View::share([
            '_title'       => $title,
            '_description' => $description,
        ]);
    }
}

if (!function_exists('sys_str_unique')) {
    /**
     * 获取通过 ',' 间隔的唯一字串, 去除空值
     * @param string $current
     * @param string $add
     * @return string
     */
    function sys_str_unique(string $current, string $add): string
    {
        // 追加
        $current .= ',' . $add;
        // 去重
        $arr = explode(',', $current);

        return collect($arr)->sort()->unique()->filter(function ($item) {
            return $item;
        })->sort()->implode(',');
    }
}


if (!function_exists('sys_str_to_json')) {
    /**
     * 字串转换为json
     * @param $string
     * @return array
     */
    function sys_str_to_json($string): array
    {
        if (is_object($string)) {
            return json_decode(json_encode($string), true);
        }
        if (UtilHelper::isJson($string)) {
            return json_decode($string, true);
        }

        return [];
    }
}

if (!function_exists('sys_array_to_json')) {
    /**
     * 字串转换为json
     * @param $string
     * @return string
     */
    function sys_array_to_json($string): string
    {
        return json_encode($string, JSON_UNESCAPED_UNICODE);
    }
}


if (!function_exists('sys_is_pjax')) {
    /**
     * 检测是否是 pjax 请求
     */
    function sys_is_pjax(): bool
    {
        return Request::pjax();
    }
}

if (!function_exists('sys_get')) {
    /**
     * 获取 data 中的数据
     * 支持批量获取
     * @param array|object      $data
     * @param string|null|array $key
     * @param string            $default
     * @return null|array|string
     */
    function sys_get($data, $key, $default = '')
    {
        if (is_array($key)) {
            $arr = Arr::only($data, $key);
            return array_map(function ($value) use ($data) {
                if (empty($value)) {
                    return '';
                }
                if (is_string($value)) {
                    return trim($value);
                }
                return $value;
            }, $arr);
        }
        $value = data_get($data, $key, $default);
        $value = is_null($value) ? $default : $value;
        if (is_string($value)) {
            return trim($value);
        }

        return $value;
    }
}

if (!function_exists('sys_parent_id')) {
    /**
     * 父级用户
     * @param PamAccount|int|string $pam
     * @return false|string
     */
    function sys_parent_id($pam)
    {
        static $rel;

        if (!$pam) {
            return 0;
        }

        if (!$rel) {
            $rel = sys_cache('py-system')->get(PySystemDef::ckPamRelParent());
        }

        $pamId = ($pam instanceof PamAccount) ? $pam->id : $pam;

        if (is_numeric($pam) && !isset($rel[$pam])) {
            /** @var PamAccount $pam */
            $pam = PamAccount::find($pam);
        }

        if (!isset($rel[$pamId])) {
            $rel[$pamId] = $pam->parent_id ?: $pam->id;
            sys_cache('py-system')->forever(PySystemDef::ckPamRelParent(), $rel);
        }

        return $rel[$pamId] ?? 0;
    }
}

if (!function_exists('sys_url')) {
    /**
     * URL生成
     * @param string|array $key   url 参数
     * @param null|string  $value 值
     * @param bool         $root  是否生成根地址
     * @return string
     */
    function sys_url($key, $value = null, $root = false)
    {
        if ($root) {
            return '?' . $key . '=' . $value;
        }
        $input = input();
        // 字串
        if (is_string($key)) {
            if ($value === null) {
                unset($input[$key]);
            }
            else {
                $input[$key] = $value;
            }
        }

        // array
        if (is_array($key)) {
            foreach ($key as $_key => $_val) {
                if ($_val === null) {
                    unset($input[$_key]);
                }
                else {
                    $input[$_key] = $_val;
                }
            }
        }

        if (is_array($input)) {
            foreach ($input as $_key => $_val) {
                if (Str::startsWith($_key, '_')) {
                    unset($input[$_key]);
                }
            }
        }

        return '?' . http_build_query($input);
    }
}


if (!function_exists('sys_is_mobile')) {
    /**
     * 检测是否是 Mobile
     * @return bool
     */
    function sys_is_mobile(): bool
    {
        $useragent = EnvHelper::agent();

        return preg_match(
                '/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|miui/i',
                $useragent
            )
            ||
            preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));
    }
}

if (!function_exists('sys_key_trim')) {
    /**
     * 对于Key来去掉 --------- PRIVATE KEY ----------
     * @param string $key
     * @return string
     */
    function sys_key_trim(string $key): string
    {
        return StrHelper::trimSpace(preg_replace('/-----.*?-----/', '', $key));
    }
}


if (!function_exists('sys_api_demo')) {
    /**
     * 是否是测试模式
     */
    function sys_api_demo(): bool
    {
        $all = input();
        if(isset($all['_py_secret']) && $all['_py_secret']) {
            return $all['_py_secret'] === config('poppy.system.secret');
        }
        return false;
    }
}

if (!function_exists('sys_content_trim')) {
    /**
     * 清空word 代码
     * @param string $content        内容
     * @param string $allowable_tags 允许保留的标签
     * @return string
     */
    function sys_content_trim(string $content, $allowable_tags = 'p,img'): string
    {
        mb_regex_encoding('UTF-8');
        //replace MS special characters first
        $search  = ['/‘/u', '/’/u', '/“/u', '/”/u', '/—/u'];
        $replace = ['\'', '\'', '"', '"', '-'];
        $content = preg_replace($search, $replace, $content);
        //make sure _all_ html entities are converted to the plain ascii equivalents - it appears
        //in some MS headers, some html entities are encoded and some aren't
        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
        //try to strip out any C style comments first, since these, embedded in html comments, seem to
        //prevent strip_tags from removing html comments (MS Word introduced combination)
        if (mb_stripos($content, '/*') !== false) {
            $content = mb_eregi_replace('#/\*.*?\*/#s', '', $content, 'm');
        }
        //introduce a space into any arithmetic expressions that could be caught by strip_tags so that they won't be
        //'<1' becomes '< 1'(note: somewhat application specific)
        $content = preg_replace(['/<([0-9]+)/'], ['< $1'], $content);

        $content = strip_tags($content, $allowable_tags);
        //eliminate extraneous whitespace from start and end of line, or anywhere there are two or more spaces, convert it to one
        $content = preg_replace(['/^\s\s+/', '/\s\s+$/', '/\s\s+/u'], ['', '', ' '], $content);
        //strip out inline css and simplify style tags
        $search  = ['#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu', '#<(em|i)[^>]*>(.*?)</(em|i)>#isu', '#<u[^>]*>(.*?)</u>#isu'];
        $replace = ['<b>$2</b>', '<i>$2</i>', '<u>$1</u>'];
        $content = preg_replace($search, $replace, $content);

        //on some of the ?newer MS Word exports, where you get conditionals of the form 'if gte mso 9', etc., it appears
        //that whatever is in one of the html comments prevents strip_tags from eradicating the html comment that contains
        //some MS Style Definitions - this last bit gets rid of any leftover comments */
        $num_matches = preg_match_all("/<!--/u", $content, $matches);
        if ($num_matches) {
            $content = preg_replace('/<!--(.)*-->/su', '', $content);
        }
        $content = preg_replace('/mso-([a-z-A-Z]*:\s?[a-z-A-Z]*;?)/i', '', $content);

        return $content;
    }
}

if (!function_exists('sys_is_demo')) {
    /**
     * 是否是 Demo 模式
     * @return bool
     */
    function sys_is_demo(): bool
    {
        return config('poppy.system.demo');
    }
}
