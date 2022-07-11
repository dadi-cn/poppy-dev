<?php

namespace Poppy\System\Action;

use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Auth\SessionGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\Framework\Exceptions\ApplicationException;
use Poppy\Framework\Validation\Rule;
use Poppy\System\Classes\Contracts\PasswordContract;
use Poppy\System\Classes\Traits\PamTrait;
use Poppy\System\Classes\Traits\UserSettingTrait;
use Poppy\System\Events\LoginBannedEvent;
use Poppy\System\Events\LoginFailedEvent;
use Poppy\System\Events\LoginSuccessEvent;
use Poppy\System\Events\PamDisableEvent;
use Poppy\System\Events\PamEnableEvent;
use Poppy\System\Events\PamPasswordModifiedEvent;
use Poppy\System\Events\PamRebindEvent;
use Poppy\System\Events\PamRegisteredEvent;
use Poppy\System\Models\PamAccount;
use Poppy\System\Models\PamLog;
use Poppy\System\Models\PamRole;
use Poppy\System\Models\SysConfig;
use Throwable;
use Tymon\JWTAuth\JWTGuard;
use Validator;

/**
 * 账号操作
 */
class Pam
{
    use UserSettingTrait, AppTrait, PamTrait;

    /**
     * @var int 父级ID
     */
    private $parentId = 0;

    /**
     * @var bool
     */
    private bool $isRegister = false;

    /**
     * @var string Pam table
     */
    private $pamTable;

    public function __construct()
    {
        $pamClass = config('poppy.core.rbac.account');
        if (!$pamClass) {
            $pamClass = PamAccount::class;
        }
        $this->pamTable = (new $pamClass())->getTable();
    }

    /**
     * @return bool
     */
    public function getIsRegister(): bool
    {
        return $this->isRegister;
    }

    /**
     * 验证验登录
     * @param string $passport 通行证
     * @param string $captcha  验证码
     * @param string $guard    认证 Guard
     * @return bool
     * @throws Throwable
     */
    public function captchaLogin(string $passport, string $captcha, string $guard): bool
    {
        $initDb = [
            'passport' => $passport,
            'captcha'  => $captcha,
            'platform' => x_header('os'),
        ];

        // 数据验证
        $validator = Validator::make($initDb, [
            'passport' => Rule::required(),
            'captcha'  => Rule::required(),
            'platform' => [
                Rule::required(),
                Rule::in(PamAccount::kvPlatform()),
            ],
        ]);
        if ($validator->fails()) {
            return $this->setError($validator->messages());
        }

        // 验证账号 + 验证码
        $verification = new Verification();

        if (!$verification->checkCaptcha($passport, $captcha)) {
            return $this->setError($verification->getError()->getMessage());
        }

        // 判定账号是否存在, 如果不存在则进行注册
        $this->pam = PamAccount::passport($passport);
        if (!$this->pam) {
            if (Str::contains($guard, ['develop', 'backend'])) {
                return $this->setError('此类账号不允许自动注册');
            }
            if (!$this->register($initDb['passport'])) {
                return false;
            }
            $this->isRegister = true;
        }

        // 检测权限, 是否被禁用
        if (!$this->checkIsEnable($this->pam)) {
            return false;
        }

        try {
            event(new LoginBannedEvent($this->pam, $guard));
        } catch (Throwable $e) {
            return $this->setError($e);
        }

        event(new LoginSuccessEvent($this->pam, $guard));
        return true;
    }

    /**
     * 设置父级ID
     * @param int $parent_id 父级id
     */
    public function setParentId(int $parent_id): void
    {
        $this->parentId = $parent_id;
    }

    /**
     * 用户注册
     * @param string           $passport  passport
     * @param string           $password  密码
     * @param string|array|int $role_name 用户角色名称
     * @return bool
     * @throws Throwable
     */
    public function register(string $passport, string $password = '', $role_name = PamRole::FE_USER): bool
    {
        $passport = PamAccount::fullFilledPassport($passport);
        $type     = PamAccount::passportType($passport);

        $initDb = [
            $type          => $passport,
            'password'     => $password,
            'reg_platform' => x_header('os'),
            'parent_id'    => $this->parentId,
        ];

        $rule = [
            $type      => [
                Rule::required(),
                Rule::string(),
                Rule::between(6, 50),
                // 唯一性认证
                Rule::unique($this->pamTable, $type),
            ],
            'password' => [
                Rule::string(),
            ],
        ];

        // 完善主账号类型规则
        if ($type === PamAccount::REG_TYPE_USERNAME) {
            if (preg_match('/\s+/', $passport)) {
                return $this->setError(trans('py-system::action.pam.user_name_not_space'));
            }
            // 注册用户时候的正则匹配
            if ($this->parentId) {
                // 子用户中必须包含 ':' 冒号
                if (strpos($initDb[$type], ':') === false) {
                    return $this->setError(trans('py-system::action.pam.sub_user_account_need_colon'));
                }
                // 初始化子用户数据
                $initDb['parent_id'] = $this->parentId;

                // 注册子用户, 子用户比主账号多一个 :
                array_unshift($rule[$type], Rule::username(true));
            }
            else {
                array_unshift($rule[$type], Rule::username());
            }
        }

        // 密码不为空时候的检测
        if ($password !== '') {
            $rule['password'] += [
                Rule::between(6, 16),
                Rule::required(),
                Rule::simplePwd(),
            ];
        }

        // 验证数据
        $validator = Validator::make($initDb, $rule);
        if ($validator->fails()) {
            return $this->setError($validator->messages());
        }

        if (is_string($role_name)) {
            $role = PamRole::whereIn('name', (array) $role_name)->get();
        }
        else {
            $roleNames = (array) $role_name;
            $role      = PamRole::whereIn('id', $roleNames)->get();
        }
        if (!$role) {
            return $this->setError(trans('py-system::action.pam.role_not_exists'));
        }

        // 自动设置前缀
        $prefix = strtoupper(strtolower(sys_setting('py-system::pam.prefix')));
        if ($type !== PamAccount::REG_TYPE_USERNAME) {
            $hasAccountName = false;
            // 检查是否设置了前缀
            if (!$prefix) {
                return $this->setError(trans('py-system::action.pam.not_set_name_prefix'));
            }
            $username = $prefix . '_' . Carbon::now()->format('YmdHis') . Str::random(6);
        }
        else {
            $hasAccountName = true;
            $username       = $passport;
        }

        $initDb['username']  = $username;
        $initDb['type']      = $role->first()->type;
        $initDb['is_enable'] = SysConfig::ENABLE;

        // 处理数据库
        DB::transaction(function () use ($initDb, $role, $password, $hasAccountName, $prefix) {
            /** @var PamAccount $pam pam */
            $pam = PamAccount::create($initDb);

            // 给用户默认角色
            $pam->roles()->attach($role->pluck('id'));

            // 如果没有设置账号, 则根据规范生成用户名
            if (!$hasAccountName) {
                $formatAccountName = sprintf("%s_%'.09d", $prefix, $pam->id);
                $pam->username     = $formatAccountName;

            }

            // 设置默认国际手机号, 后台自动生成(Backend 用户/Develop)
            if (in_array($initDb['type'], [PamAccount::TYPE_BACKEND, PamAccount::TYPE_DEVELOP]) && !isset($initDb['mobile'])) {
                $pam->mobile = PamAccount::dftMobile($pam->id);
            }

            // 设置密码
            if ($password) {
                $key               = Str::random(6);
                $regDatetime       = $pam->created_at->toDateTimeString();
                $pam->password     = app(PasswordContract::class)->genPassword($password, $regDatetime, $key);
                $pam->password_key = $key;
            }

            $pam->save();

            // 触发注册成功的事件
            event(new PamRegisteredEvent($pam));

            $this->pam = $pam;

        });
        return true;
    }

    /**
     * 密码登录
     * @param string $passport   passport
     * @param string $password   密码
     * @param string $guard_name 类型
     * @return bool
     * @throws ApplicationException
     */
    public function loginCheck(string $passport, string $password, string $guard_name = PamAccount::GUARD_WEB): bool
    {
        $type        = PamAccount::passportType($passport);
        $credentials = [
            $type      => $passport,
            'password' => $password,
        ];

        // check exists
        $validator = Validator::make($credentials, [
            $type      => [
                Rule::required(),
            ],
            'password' => Rule::required(),
        ], []);
        if ($validator->fails()) {
            return $this->setError($validator->errors());
        }

        $guard = Auth::guard($guard_name);

        if ($guard->attempt($credentials)) {
            // jwt 不能获取到 user， 使用 getLastAttempted 方法来获取数据
            if ($guard instanceof JWTGuard || $guard instanceof SessionGuard) {
                /** @var PamAccount $pam */
                $pam = $guard->user();
            }
            else {
                throw new ApplicationException('未知的 guard');
            }
            $this->pam = $pam;

            if (!$this->checkIsEnable($this->pam)) {
                return false;
            }

            try {
                event(new LoginBannedEvent($this->pam, $guard));
            } catch (Throwable $e) {
                return $this->setError($e);
            }

            event(new LoginSuccessEvent($pam, $guard_name));
            return true;
        }

        $credentials += [
            'type'     => $type,
            'passport' => $passport,
        ];

        event(new LoginFailedEvent($credentials));

        return $this->setError(trans('py-system::action.pam.login_fail_again'));

    }

    /**
     * 设置登录密码
     * @param PamAccount|mixed $pam      用户
     * @param string           $password 密码
     * @return bool
     */
    public function setPassword($pam, string $password): bool
    {
        if (is_string($pam)) {
            $pam = PamAccount::passport($pam);
        }

        if (!($pam instanceof PamAccount)) {
            return $this->setError(trans('py-system::action.pam.pam_error'));
        }
        $validator = Validator::make([
            'password' => $password,
        ], [
            'password' => [
                Rule::string(),
                Rule::required(),
                Rule::simplePwd(),
                Rule::between(6, 20),
            ],
        ]);
        if ($validator->fails()) {
            return $this->setError($validator->messages());
        }

        $key               = Str::random(6);
        $regDatetime       = $pam->created_at->toDateTimeString();
        $cryptPassword     = app(PasswordContract::class)->genPassword($password, $regDatetime, $key);
        $pam->password     = $cryptPassword;
        $pam->password_key = $key;
        $pam->save();

        event(new PamPasswordModifiedEvent($pam));

        return true;
    }

    /**
     * 设置角色
     * @param PamAccount|mixed $pam   账号数据
     * @param array            $roles 角色名
     * @return bool
     */
    public function setRoles($pam, array $roles): bool
    {
        /** @var PamRole[]|Collection $role */
        $role = PamRole::whereIn('id', $roles)->get();
        $pam->roles()->detach();
        $pam->roles()->attach($role->pluck('id'));

        return true;
    }

    /**
     * 生成支持 passport 格式的数组
     * @param array|Request $credentials 待转化的数据
     * @return array
     */
    public function passportData($credentials): array
    {
        if ($credentials instanceof Request) {
            $credentials = $credentials->all();
        }
        $passport     = $credentials['passport'] ?? '';
        $passport     = $passport ?: $credentials['mobile'] ?? '';
        $passport     = $passport ?: $credentials['username'] ?? '';
        $passport     = $passport ?: $credentials['email'] ?? '';
        $passportType = PamAccount::passportType($passport);

        return [
            $passportType => $passport,
            'password'    => $credentials['password'] ?? '',
        ];
    }


    /**
     * 更换账号主体, 支持除非ID外的更换方式
     * @param string|numeric|PamAccount $old_passport
     * @param string                    $new_passport
     * @return bool
     */
    public function rebind($old_passport, string $new_passport): bool
    {
        $pam = null;
        if (PamAccount::passportExists($new_passport)) {
            return $this->setError('账号已存在, 无法更换');
        }
        if (is_numeric($old_passport) || is_string($old_passport)) {
            $old_passport = PamAccount::fullFilledPassport($old_passport);
            $pam          = PamAccount::passport($old_passport);
        }
        else if ($old_passport instanceof PamAccount) {
            $pam = $old_passport;
        }
        if (!$pam) {
            return $this->setError('原账号不存在, 无法更换');
        }
        $newPassportType = PamAccount::passportType($new_passport);
        if ($newPassportType === 'id') {
            return $this->setError('用户ID 无法更换, 请检查输入');
        }
        $pam->{$newPassportType} = PamAccount::fullFilledPassport($new_passport);
        $pam->save();

        event(new PamRebindEvent($pam));
        return true;
    }

    /**
     * 后台用户禁用
     * @param int    $id     用户id
     * @param string $to     解禁时间
     * @param string $reason 禁用原因
     * @return bool
     */
    public function disable(int $id, string $to, string $reason): bool
    {
        $data      = [
            'disable_reason' => $reason,
            'disable_to'     => $to,
        ];
        $validator = Validator::make($data, [
            'disable_reason' => [
                Rule::string(),
            ],
            'disable_to'     => [
                Rule::string(),
                Rule::dateFormat('Y-m-d H:i:s'),
            ], [], [
                'disable_reason' => trans('py-system::action.pam.disable_reason'),
                'disable_to'     => trans('py-system::action.pam.disable_to'),
            ],
        ]);
        if ($validator->fails()) {
            return $this->setError($validator->messages());
        }

        /** @var PamAccount $pam */
        $pam = PamAccount::find($id);
        //当前用户已禁用
        if (!$pam->is_enable) {
            return $this->setError(trans('py-system::action.pam.account_disabled'));
        }

        $disableTo = Carbon::parse($data['disable_to']);
        if ($disableTo->lessThan(Carbon::now())) {
            return $this->setError('解禁日期需要大于当前日期');
        }
        $pam->update([
            'is_enable'        => SysConfig::DISABLE,
            'disable_reason'   => $data['disable_reason'],
            'disable_start_at' => Carbon::now(),
            'disable_end_at'   => $disableTo->toDateTimeString(),
        ]);

        event(new PamDisableEvent($pam, $this->pam, $reason));

        return true;
    }

    /**
     * 后台用户启用
     * @param int    $id     用户Id
     * @param string $reason 原因
     * @return bool
     */
    public function enable($id, $reason = ''): bool
    {
        $pam = PamAccount::find($id);
        if (!$pam) {
            return $this->setError('用户不存在');
        }
        if ($pam->is_enable === SysConfig::YES) {
            return $this->setError(trans('py-system::action.pam.account_enabled'));
        }

        $pam->is_enable = SysConfig::ENABLE;
        $pam->save();

        event(new PamEnableEvent($pam, $this->pam, $reason));

        return true;
    }

    /**
     * 自动解禁
     */
    public function autoEnable(): bool
    {
        $Db  = PamAccount::where([
            'is_enable' => SysConfig::DISABLE,
        ])->where('disable_end_at', '<=', Carbon::now());
        $res = (clone $Db)->exists();
        if ($res) {
            $items = $Db->get();
            foreach ($items as $item) {
                $item->is_enable = SysConfig::ENABLE;
                $item->save();
                event(new PamEnableEvent($item, null, '系统自动解禁'));
            }
        }

        return true;
    }

    /**
     * 清除登录日志
     * @return bool
     */
    public function clearLog(): bool
    {
        // 删除 60 天以外的登录日志
        PamLog::where('created_at', '<', Carbon::now()->subDays(60))->delete();
        return true;
    }

    /**
     * 修改密码
     * @param string $old_password 老密码
     * @param string $password     新密码
     * @return bool
     */
    public function changePassword($old_password, $password): bool
    {
        if (!$this->checkPam()) {
            return false;
        }
        $old_password = trim($old_password);
        $password     = trim($password);

        if ($old_password === $password) {
            return $this->setError('新旧密码不能相同');
        }

        if (!app(PasswordContract::class)->check($this->pam, $old_password)) {
            return $this->setError('旧密码不正确');
        }

        return $this->setPassword($this->pam, $password);
    }

    /**
     * 验证用户权限
     * @param PamAccount $pam 用户
     * @return bool
     */
    private function checkIsEnable(PamAccount $pam): bool
    {
        if ($pam->is_enable === SysConfig::NO) {
            $now = Carbon::now();
            // 当前时间大于禁用时间(已解禁)
            if ($now->gt($pam->disable_end_at)) {
                $this->enable($pam->id, '用户登录, 超过封禁时间, 自动解禁');
                return true;
            }
            return $this->setError("该账号因 $pam->disable_reason 被封禁至 $pam->disable_end_at");
        }
        return true;
    }
}
