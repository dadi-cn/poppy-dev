<?php

use xingwenge\canal_php\CanalClient;

return [

    'system' => [

        /* 用户默认跳转地址
         * ---------------------------------------- */
        'user_location'  => '/login',

        /*
        |--------------------------------------------------------------------------
        | 跨域来源
        |--------------------------------------------------------------------------
        |
        */
        'cross_origin'   => '*',

        /*
        |--------------------------------------------------------------------------
        | 单点登录
        |--------------------------------------------------------------------------
        */
        'sso'            => true,

        /*
        |--------------------------------------------------------------------------
        | 允许的Header
        |--------------------------------------------------------------------------
        |
        */
        'cross_headers'  => [
            // sentry
            'sentry-trace',
            // app
            'x-app', 'x-app-host', 'x-app-sign',
            // common software
            'x-os', 'x-ver', 'x-id', 'x-type',
            // system
            'x-sys-name', 'x-sys-version', 'x-sys-device', 'x-sys-cpu', 'x-sys-network',
            // append
            'x-k1', 'x-k2', 'x-k3', 'x-k4', 'x-k5',
            'x-k6', 'x-k7', 'x-k8', 'x-k9', 'x-k10',
        ],

        /*
        |--------------------------------------------------------------------------
        | 接口debug key, 当 _py_secret 和此值相等, 则不进行加密的签名验证
        |--------------------------------------------------------------------------
        |
        */
        'secret'         => env('PY_SECRET', ''),

        /*
        |--------------------------------------------------------------------------
        | 验证码长度
        |--------------------------------------------------------------------------
        */
        'captcha_length' => 4,


        /* 用户模块类型映射
         * ---------------------------------------- */
        'role_type_map'  => [
            'desktop' => 'backend',
            'front'   => 'user',
        ],
    ],

    'core' => [
        'op_mail' => 'zhaody901@126.com',

        /*
        |--------------------------------------------------------------------------
        | 接口文档的定义
        |--------------------------------------------------------------------------
        | 需要运行 `php artisan py-core:doc api` 来生成技术文档
        */
        'apidoc'  => [
            'web'     => [
                // 标题
                'title'       => '用户接口',
                // 默认访问地址
                'default_url' => 'api_v1/system/auth/login',

                'method' => 'post',

                'sign_token' => true,
                'match'      => 'api.*/web|ApiWeb|api/web|ApiV1',
            ],
            'dev'     => [
                'title'       => '开发平台',
                'default_url' => 'api/mgr-dev/apidoc/json',
                'method'      => 'post',
                'match'       => 'api.*/dev|ApiDev|api/dev',

            ],
            'mgr-app' => [
                'title'       => '后台管理',
                'default_url' => 'api/mgr-app/default/ban',
                'method'      => 'post',
                'match'       => 'ApiMgrApp|api/mgr_app|api.*/mgr_app',
            ],
        ],
    ],

    'framework' => [

        /*
        |--------------------------------------------------------------------------
        | Seo 相关
        |--------------------------------------------------------------------------
        |
        */
        'title' => '网站名称',


        'description' => '网站描述',
    ],

    'sms' => [
        'sign'  => '氪金兽',

        /* 短信类型
         * ---------------------------------------- */
        'types' => [
            [
                'type'  => 'captcha',
                'title' => '验证码(:code)',
            ],
            [
                'type'  => 'captcha-cty',
                'title' => '国际验证码(:code)',
            ],
            [
                'type'  => 'handle',
                'title' => '接单成功通知',
            ],
        ],
    ],

    'canal-es' => [
        'canal' => [
            'client_type'     => CanalClient::TYPE_SWOOLE,
            'host'            => env('CANAL_HOST', '127.0.0.1'),
            'port'            => env('CANAL_PORT', 11111),
            'client_id'       => env('CANAL_CLIENT_ID', 1001),
            'connect_timeout' => env('CANAL_CONNECT_TIMEOUT', 10),
            'message_size'    => 100,
        ],


        'elasticsearch' => [
            'concurrency' => env('ELASTICSEARCH_CONCURRENCY', 100),

            'hosts' => value(function () {
                $settings = env('ELASTICSEARCH_HOSTS');
                $hosts    = array_filter(explode(';', $settings));

                return $hosts ? array_map(function ($url) {
                    return array_merge(parse_url($url), [
                        'user' => env('ELASTICSEARCH_USER', null),
                        'pass' => env('ELASTICSEARCH_PASS', null),
                    ]);
                }, $hosts) : [
                    [
                        'host'   => '127.0.0.1',
                        'port'   => '9200',
                        'scheme' => 'http',
                        'user'   => env('ELASTICSEARCH_USER', null),
                        'pass'   => env('ELASTICSEARCH_PASS', null),
                    ],
                ];
            }),
        ],
    ],
];