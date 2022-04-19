<?php

namespace Poppy\MgrApp\Http\Request\ApiDev;

use Poppy\Framework\Classes\Resp;
use Poppy\MgrApp\Classes\Contracts\Checkable;
use Poppy\MgrApp\Classes\Form\SettingBase;

class ConfigController extends DevelopController
{
    private array $settings = [];

    private array $envs = [];

    public function check()
    {
        // 环境检测
        $errors = [];
        $this->checkEnv();
        if (count($this->envs)) {
            $errors[] = [
                'title'   => '环境检测',
                'content' => $this->envs,
            ];
        }
        if (!count($errors)) {
            return Resp::success('检测通过');
        }

        // 配置检测
        $hooks = sys_hook('poppy.mgr-app.settings');
        foreach ($hooks as $forms) {
            collect($forms['forms'])->map(function ($form_class) {
                $this->checkForm($form_class);
            });
        }
        if (count($this->settings)) {
            $errors[] = [
                'title'   => '配置检测',
                'content' => $this->settings,
            ];
        }

        // 项目检测
        $checkers = sys_hook('poppy.mgr-dev.checker');
        $ckErrors = [];
        if (is_array($checkers)) {
            foreach ($checkers as $checker) {
                collect($checker['checker'])->each(function ($checker_class) use (&$ckErrors) {
                    /** @var Checkable $checker */
                    $checker = new $checker_class();
                    if ($errors = $checker->check()) {
                        $ckErrors = array_merge($ckErrors, $errors);
                    }
                });
            }
        }
        if (count($ckErrors)) {
            $errors[] = [
                'title'   => '项目检测',
                'content' => $ckErrors,
            ];
        }

        return Resp::error('系统检测', $errors);
    }

    private function checkForm($form_class)
    {
        if (!class_exists($form_class)) {
            return;
        }
        /** @var SettingBase $form */
        $form = new $form_class();
        if (!method_exists($form, 'form')) {
            return;
        }
        $form->form();
        $group    = $form->getGroup();
        $settings = app('poppy.system.setting')->getNG($group);
        if ($messages = $form->validate($settings)) {
            foreach ($messages->messages() as $name => $message) {
                $this->settings[] = [
                    'title' => "设置项 `{$group}.{$name}`:" . implode(',', $message),
                    'type'  => 'error',
                ];
            }
        }
    }

    /**
     * 配置项检测
     */
    public function checkEnv()
    {
        $env = [
            'JWT_SECRET' => 'Jwt Token 授权会出现获取Token 认证失败的情况',
            'APP_ENV'    => '会出现 logger 无法使用的情况',
            'APP_KEY'    => '会出现 RuntimeException 错误 (The only supported ciphers are AES-128-CBC and AES-256-CBC with the correct key lengths)',
        ];

        foreach ($env as $_env => $desc) {
            if (!env($_env)) {
                $this->envs[] = [
                    'title' => "环境变量 `{$_env}` 不存在, {$desc}",
                    'type'  => 'error',
                ];
            }
        }

        $env = [
            'apidoc' => '无法生成 apidoc 文档',
        ];

        foreach ($env as $_env => $desc) {
            if (!command_exist($_env)) {
                $this->envs[] = [
                    'title' => "命令 `{$_env}` 不存在, {$desc}",
                    'type'  => 'warning',
                ];
            }
        }

        $env = [
            'gd'       => '无法上传图片',
            'json'     => '无法正常返回接口数据',
            'iconv'    => '对于字符集之间的编解码可能会出现错误',
            'mbstring' => '对于多字符截取可能会出现错误',
            'bcmath'   => '对于高精度数学运算可能会出现错误',
            'fileinfo' => '对于上传图片无法检测文件MIME类型',
        ];

        foreach ($env as $_env => $desc) {
            if (!extension_loaded($_env)) {
                $this->envs[] = [
                    'title' => "扩展 `{$_env}` 不存在 , {$desc}",
                    'type'  => 'error',
                ];
            }
        }
    }
}