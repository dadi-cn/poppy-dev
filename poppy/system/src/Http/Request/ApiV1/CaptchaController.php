<?php

namespace Poppy\System\Http\Request\ApiV1;

use Poppy\Framework\Classes\Resp;
use Poppy\System\Action\Verification;
use Poppy\System\Events\CaptchaSendEvent;
use Poppy\System\Models\PamAccount;
use Throwable;

/**
 * 验证码控制器
 */
class CaptchaController extends JwtApiController
{

    /**
     * @api                   {post} api_v1/system/captcha/send [Sys]发送验证码
     * @apiVersion            1.0.0
     * @apiName               SysCaptchaSend
     * @apiGroup              Poppy
     * @apiQuery {String}     passport       通行证
     * @apiQuery {String}     [type]         是否存在[exist:验证必须存在;no-exist:验证必须不存在]
     */
    public function send()
    {
        $input    = input();
        $passport = sys_get($input, 'passport');
        $type     = sys_get($input, 'type');

        if ($type) {
            if ($type === 'exist') {
                if (!PamAccount::passportExists($passport)) {
                    return Resp::error('输入的账号不存在, 请检查输入');
                }
            }
            elseif ($type === 'no-exist') {
                if (PamAccount::passportExists($passport)) {
                    return Resp::error('输入的账号已存在, 请检查输入');
                }
            }
            else {
                return Resp::error('验证类型有误,请检查输入');
            }
        }

        $Verification = new Verification();
        $expired      = (int) sys_setting('py-system::pam.captcha_expired') ?: 5;
        $length       = (int) sys_setting('py-system::pam.captcha_length') ?: 6;
        if ($Verification->genCaptcha($passport, $expired, $length)) {
            $captcha = $Verification->getCaptcha();
            try {
                event(new CaptchaSendEvent($passport, $captcha));
                return Resp::success('验证码发送成功' . (!is_production() ? ', 验证码:' . $captcha : ''));
            } catch (Throwable $e) {
                return Resp::error($e);
            }
        }
        else {
            return Resp::error($Verification->getError());
        }
    }

    /**
     * @api                   {post} api_v1/system/captcha/fetch [Sys][L]获取验证码
     * @apiVersion            1.0.0
     * @apiName               SysCaptchaFetch
     * @apiGroup              Poppy
     * @apiQuery {Integer}    passport            通行证
     */
    public function fetch()
    {
        if (is_production()) {
            return Resp::error('Prod 环境不返回数据');
        }
        $passport = input('passport');

        $Verification = new Verification();
        if ($Verification->fetchCaptcha($passport)) {
            $captcha = $Verification->getCaptcha();
            return Resp::success('获取验证码成功', [
                'captcha' => $captcha,
            ]);
        }

        return Resp::error($Verification->getError());
    }

    /**
     * @api                   {post} api_v1/system/captcha/verify_code [Sys]获取验证串
     * @apiDescription        用以保存 passport 验证的验证串, 隐藏字串为 passport
     * @apiVersion            1.0.0
     * @apiName               SysCaptchaVerifyCode
     * @apiGroup              Poppy
     * @apiQuery {String}     passport           通行证
     * @apiQuery {String}     captcha            验证码
     * @apiQuery {String}     [expire_min]       验证串有效期[默认:10 分钟, 最长不超过 60 分钟]
     */
    public function verifyCode()
    {
        $passport   = (string) input('passport');
        $captcha    = (string) input('captcha');
        $expire_min = (int) input('expire_min', 10);
        if ($expire_min > 60) {
            $expire_min = 60;
        }
        if ($expire_min < 1) {
            $expire_min = 1;
        }

        $Verification = new Verification();
        if (!$Verification->checkCaptcha($passport, $captcha)) {
            return Resp::error($Verification->getError());
        }
        $onceCode = $Verification->genOnceVerifyCode($expire_min, $passport);
        return Resp::success('生成验证串', [
            'verify_code' => $onceCode,
        ]);
    }
}