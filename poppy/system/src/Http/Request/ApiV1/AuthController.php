<?php

namespace Poppy\System\Http\Request\ApiV1;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\Framework\Validation\Rule;
use Poppy\System\Action\Pam;
use Poppy\System\Action\Sso;
use Poppy\System\Action\Verification;
use Poppy\System\Events\LoginTokenPassedEvent;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\Resources\PamResource;
use Throwable;
use Validator;

/**
 * 认证控制器
 */
class AuthController extends JwtApiController
{
    use PoppyTrait, ThrottlesLogins;

    /**
     * @api                   {post} api_v1/system/auth/access [Sys]检测 Token
     * @apiVersion            1.0.0
     * @apiName               SysAuthAccess
     * @apiGroup              Poppy
     * @apiQuery {integer}    token           Token
     * @apiSuccess {object[]} data            返回
     * @apiSuccess {integer}  id              ID
     * @apiSuccess {string}   username        用户名
     * @apiSuccess {string}   mobile          手机号
     * @apiSuccess {string}   email           邮箱
     * @apiSuccess {string}   type            类型
     * @apiSuccess {string}   is_enable       是否启用[Y|N]
     * @apiSuccess {string}   disable_reason  禁用原因
     * @apiSuccess {string}   created_at      创建时间
     * @apiSuccessExample {json} data:
     * {
     *     "status": 0,
     *     "message": "",
     *     "data": {
     *         "id": 9,
     *         "username": "user001",
     *         "mobile": "",
     *         "email": "",
     *         "type": "user",
     *         "is_enable": "Y",
     *         "disable_reason": "",
     *         "created_at": "2021-03-18 15:30:15",
     *         "updated_at": "2021-03-18 16:38:06"
     *     }
     * }
     */
    public function access(): JsonResponse
    {
        return Resp::success(
            '有效登录',
            (new PamResource($this->pam()))->toArray(app('request'))
        );
    }

    /**
     * @api                   {post} api_v1/system/auth/login [Sys]登录/注册
     * @apiVersion            1.0.0
     * @apiName               SysAuthLogin
     * @apiGroup              Poppy
     * @apiQuery {string}     guard           登录类型;web|Web;backend|后台;develop|开发者
     * @apiQuery {string}     passport        通行证
     * @apiQuery {string}     [password]      密码
     * @apiQuery {string}     [captcha]       验证码
     * @apiQuery {string}     [device_id]     设备ID[开启单一登录之后可用]
     * @apiQuery {string}     [device_type]   设备类型[开启单一登录之后可用]
     * @apiQuery {string}     [guard]         登录前台/后台, 默认是前台
     * @apiSuccess {object[]} data            返回
     * @apiSuccess {string}   token           认证成功的Token
     * @apiSuccess {string}   type            账号类型
     * @apiSuccessExample     {json} data:
     * {
     *     "status": 0,
     *     "message": "",
     *     "data": {
     *         "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.*******",
     *         "type": "backend"
     *     }
     * }
     */
    public function login(): JsonResponse
    {
        $validator = Validator::make($this->pyRequest()->all(), [
            'passport' => Rule::required(),
        ], [
            'passport.required' => '通行证必须填写',
        ]);
        if ($validator->fails()) {
            return Resp::error($validator->messages());
        }

        $passport = PamAccount::fullFilledPassport(input('passport', ''));
        $captcha  = input('captcha', '');
        $password = input('password', '');

        if (!$captcha && !$password) {
            return Resp::error('登录密码或者验证码必须填写');
        }

        /** @var ResponseFactory $response */
        $response = app(ResponseFactory::class);
        if ($this->hasTooManyLoginAttempts($this->pyRequest())) {
            $seconds = $this->limiter()->availableIn($this->throttleKey($this->pyRequest()));
            $message = $this->pyTranslator()->get('auth.throttle', ['seconds' => $seconds]);

            return $response->json([
                'message' => $message,
                'status'  => 401,
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $type  = (input('guard') ?: x_header('type'));
        $guard = PamAccount::GUARD_JWT_WEB;
        if ($type === 'backend') {
            $guard = PamAccount::GUARD_JWT_BACKEND;
        }
        elseif ($type === 'develop') {
            $guard = PamAccount::GUARD_JWT_DEVELOP;
        }

        $Pam = new Pam();
        try {
            if ($captcha) {
                if (!$Pam->captchaLogin($passport, $captcha, $guard)) {
                    return Resp::error($Pam->getError());
                }
            }
            elseif (!$Pam->loginCheck($passport, $password, $guard)) {
                return Resp::error($Pam->getError());
            }
        } catch (Throwable $e) {
            return Resp::error($e);
        }

        $this->clearLoginAttempts($this->pyRequest());
        $pam = $Pam->getPam();

        if (!$token = app('tymon.jwt.auth')->fromUser($pam)) {
            return Resp::error('获取 Token 失败, 请联系管理员');
        }

        /* 设备单一性登陆验证(基于 Redis + Db)
         * ---------------------------------------- */
        try {
            $deviceId   = x_header('id') ?: input('device_id', '');
            $deviceType = x_header('os') ?: input('device_type', '');
            event(new LoginTokenPassedEvent($pam, $token, $deviceId, $deviceType));
        } catch (Throwable $e) {
            return Resp::error($e->getMessage());
        }

        return Resp::success('登录成功', [
            'token'       => $token,
            'type'        => $pam->type,
            'is_register' => $Pam->getIsRegister() ? 'Y' : 'N',
        ]);
    }


    /**
     * @api                   {post} api_v1/system/auth/reset_password [Sys]重设密码
     * @apiVersion            1.0.0
     * @apiName               SysAuthResetPassword
     * @apiGroup              Poppy
     * @apiQuery {string}     [verify_code]     方式1: 通过验证码获取到-> 验证串
     * @apiQuery {string}     [passport]        方式2: 手机号 + 验证码直接验证并修改
     * @apiQuery {string}     [captcha]         验证码
     * @apiQuery {string}     password          密码
     */
    public function resetPassword()
    {
        $verify_code = input('verify_code', '');
        $password    = input('password', '');
        $passport    = input('passport', '');
        $captcha     = input('captcha', '');

        $Verification = new Verification();
        if (!$password) {
            return Resp::error('密码必须填写');
        }

        if ((!$verify_code && !$passport) || ($verify_code && $passport)) {
            return Resp::error('请选一种方式重设密码!');
        }

        $validator = Validator::make([
            'password' => $password,
        ], [
            'password' => 'required|between:6,20',
        ]);
        if ($validator->fails()) {
            return Resp::error($validator->messages());
        }

        if ($passport) {
            if (!$captcha || !$Verification->checkCaptcha($passport, $captcha)) {
                return Resp::error('请输入正确验证码');
            }
        }

        if ($verify_code) {
            if (!$Verification->verifyOnceCode($verify_code)) {
                return Resp::error($Verification->getError());
            }
            $passport = $Verification->getHidden();
        }

        $Pam = new Pam();
        if ($Pam->setPassword($passport, $password)) {
            return Resp::success('密码已经重新设置');
        }

        return Resp::error($Pam->getError());
    }

    /**
     * @api                   {post} api_v1/system/auth/bind_mobile [Sys]换绑手机
     * @apiVersion            1.0.0
     * @apiName               SysAuthBindMobile
     * @apiGroup              Poppy
     * @apiQuery {string}     verify_code     之前手机号生成的校验验证串
     * @apiQuery {string}     passport        新手机号
     * @apiQuery {string}     captcha         验证码
     */
    public function bindMobile()
    {
        $captcha     = input('captcha');
        $passport    = input('passport');
        $verify_code = input('verify_code');

        if (!UtilHelper::isMobile($passport)) {
            return Resp::error('请输入正确手机号');
        }

        $Verification = new Verification();
        if (!$Verification->checkCaptcha($passport, $captcha)) {
            return Resp::error('请输入正确验证码');
        }

        if ($verify_code && !$Verification->verifyOnceCode($verify_code)) {
            return Resp::error($Verification->getError());
        }

        $hidden = $Verification->getHidden();

        $Pam = new Pam();
        if (!$Pam->rebind($hidden, $passport)) {
            return Resp::error($Pam->getError());
        }
        return Resp::success('成功绑定手机');
    }

    /**
     * @api                   {post} api_v1/system/auth/renew [Sys]续期
     * @apiVersion            1.0.0
     * @apiName               SysAuthRenew
     * @apiGroup              Poppy
     */
    public function renew()
    {
        $pam = $this->pam;
        if (!$token = app('tymon.jwt.auth')->fromUser($pam)) {
            return Resp::error('获取 Token 失败, 请联系管理员');
        }

        try {
            $deviceId   = x_header('app-id') ?: input('device_id', '');
            $deviceType = x_header('app-os') ?: input('device_type', '');
            event(new LoginTokenPassedEvent($pam, $token, $deviceId, $deviceType));
        } catch (Throwable $e) {
            return Resp::error($e->getMessage());
        }

        return Resp::success('登录成功', [
            'token' => $token,
            'type'  => $pam->type,
        ]);
    }


    /**
     * @api                   {post} api_v1/system/auth/logout [Sys]退出登录
     * @apiVersion            1.0.0
     * @apiName               SysAuthLogout
     * @apiGroup              Poppy
     */
    public function logout()
    {
        $token = jwt_token();
        $Sso   = new Sso();
        if (!$Sso->logout($token)) {
            return Resp::error($Sso->getError());
        }
        return Resp::success('已退出登录');
    }

    /**
     * @api                   {post} api_v1/system/auth/exists 检查通行证是否存在
     * @apiVersion            1.0.0
     * @apiName               SysAuthExists
     * @apiGroup              Poppy
     * @apiQuery {string}     passport 通行证
     */
    public function exists()
    {
        $passport = input('passport');
        $exists   = PamAccount::passportExists($passport);
        if ($exists) {
            return Resp::success('通行证存在');
        }

        return Resp::error('通行证不存在');
    }

    protected function username(): string
    {
        return 'passport';
    }
}