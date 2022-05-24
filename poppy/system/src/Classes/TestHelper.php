<?php

namespace Poppy\System\Classes;

use Poppy\System\Action\Verification;

/**
 * 测试帮助
 */
class TestHelper
{
    /**
     * 帮助获取器
     * captcha : 获取服务端的验证码
     * @param string $helper
     * @return string
     */
    public static function generate(string $helper): string
    {
        if (is_production()) {
            return '';
        }
        $parsed = explode('|', $helper);
        $method = array_shift($parsed);
        $tree   = new self();
        if (is_callable([$tree, $method])) {
            return call_user_func_array([$tree, $method], $parsed);
        }
        return 'method `' . $method . '` not support';
    }


    /**
     * 获取Captcha
     * @param string $passport 通行证
     * @return string
     */
    public function captcha(string $passport): string
    {
        $Verification = new Verification();
        if ($Verification->fetchCaptcha($passport)) {
            return $Verification->getCaptcha();
        }
        return '';
    }
}