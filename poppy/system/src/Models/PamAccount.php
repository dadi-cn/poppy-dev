<?php

namespace Poppy\System\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Auth\Authenticatable as TraitAuthenticatable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Poppy\Core\Rbac\Traits\RbacUserTrait;
use Poppy\Framework\Helper\UtilHelper;
use Tymon\JWTAuth\Contracts\JWTSubject as JWTSubjectAuthenticatable;

/**
 * 用户账号
 *
 * @property int $id
 * @property string $mobile             手机号
 * @property string $username           用户名称
 * @property string $password           用户密码
 * @property string|null $password_key       账号注册时候随机生成的6位key
 * @property Carbon $logined_at         登录时间
 * @property int $login_times        登录次数
 * @property string $reg_ip             注册IP
 * @property string $login_ip           当前登录IP
 * @property int $parent_id          父ID
 * @property int $is_enable          是否启用
 * @property string|null $type               类型
 * @property string|null $email              邮箱
 * @property string|null $reg_platform       注册平台
 * @property string $disable_reason     禁用原因
 * @property string|null $disable_start_at   禁用开始时间
 * @property string|null $disable_end_at     禁用结束时间
 * @property string $remember_token     Token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read PamRoleAccount $role
 * @property-read Collection|PamRole[] $roles
 * @mixin Eloquent
 */
class PamAccount extends Model implements Authenticatable, JWTSubjectAuthenticatable
{
    use TraitAuthenticatable, RbacUserTrait, Authorizable, Notifiable;

    /* Register Type
     -------------------------------------------- */
    const TYPE_BACKEND = 'backend';
    const TYPE_USER    = 'user';
    const TYPE_DEVELOP = 'develop';

    /* Register By
     -------------------------------------------- */
    const REG_TYPE_USERNAME = 'username';
    const REG_TYPE_MOBILE   = 'mobile';
    const REG_TYPE_EMAIL    = 'email';

    /* Guard Type
     -------------------------------------------- */
    const GUARD_WEB         = 'web';
    const GUARD_BACKEND     = 'backend';
    const GUARD_DEVELOP     = 'develop';
    const GUARD_USER        = 'user';
    const GUARD_JWT_BACKEND = 'jwt_backend';
    const GUARD_JWT_DEVELOP = 'jwt_develop';
    const GUARD_JWT_WEB     = 'jwt_web';
    const GUARD_JWT         = 'jwt';

    /* Register Platform
     -------------------------------------------- */
    const REG_PLATFORM_IOS     = 'ios';
    const REG_PLATFORM_ANDROID = 'android';
    const REG_PLATFORM_WEB     = 'web';
    const REG_PLATFORM_PC      = 'pc';
    const REG_PLATFORM_H5      = 'h5';
    const REG_PLATFORM_WEAPP   = 'weapp';
    const REG_PLATFORM_WEBAPP  = 'webapp';
    const REG_PLATFORM_MGRAPP  = 'mgrapp';

    protected $table = 'pam_account';

    protected $dates = [
        'logined_at',
        'disable_start_at',
        'disable_end_at',
    ];

    protected $fillable = [
        'mobile',
        'email',
        'username',
        'parent_id',
        'password',
        'type',
        'login_ip',
        'logined_at',
        'is_enable',
        'password_key',
        'reg_ip',
        'reg_platform',
        'disable_reason',
        'disable_start_at',
        'disable_end_at',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'user' => [
                'id'   => $this->id,
                'type' => $this->type,
                'salt' => md5(sha1($this->password_key) . $this->password),
            ],
        ];
    }


    /**
     * 通行证类型(可能返回不匹配的通行证类型)
     * @param string $passport 通行证
     * @return string
     */
    public static function passportType(string $passport): string
    {
        if (UtilHelper::isMobile($passport)) {
            $type = PamAccount::REG_TYPE_MOBILE;
        }
        elseif (UtilHelper::isEmail($passport)) {
            $type = PamAccount::REG_TYPE_EMAIL;
        }
        else {
            $type = PamAccount::REG_TYPE_USERNAME;
        }
        return $type;
    }

    /**
     * 补足 86 手机号
     * @param $passport
     * @return string
     */
    public static function fullFilledPassport($passport): string
    {
        $passport = preg_replace('/\s+/', '', $passport);
        // lower
        $passport = strtolower($passport);
        // mobile
        if (UtilHelper::isChMobile($passport)) {
            return '86-' . substr($passport, -11);
        }
        return $passport;
    }

    /**
     * 根据passport返回Pam
     * @param string|numeric $passport 通行证
     * @return Model|null|object|PamAccount
     */
    public static function passport($passport)
    {
        $passport = self::fullFilledPassport($passport);
        $type     = self::passportType($passport);
        return self::where($type, $passport)->first();
    }


    /**
     * 验证通行证是否存在, 自动补足 86
     * @param $passport
     * @return bool
     */
    public static function passportExists($passport): bool
    {
        $passport = self::fullFilledPassport($passport);
        $type     = self::passportType($passport);
        return self::where($type, $passport)->exists();
    }


    /**
     * 获取用户所有的 permission
     * @param self $pam pam
     * @return Collection
     */
    public static function permissions(self $pam): Collection
    {
        return $pam->cachedRoles()->reduce(function (Collection $carry, PamRole $item) {
            $item->cachedPermissions()->each(function ($item) use ($carry) {
                $carry->push($item);
            });

            return $carry;
        }, collect());
    }

    /**
     * 获取定义的 kv 值
     * @param null|string $key 需要获取的key, 默认返回整个定义
     * @param bool $check_key 检测当前key 是否存在
     * @return array|string
     */
    public static function kvType($key = null, $check_key = false)
    {
        $desc = [
            self::TYPE_USER    => '用户',
            self::TYPE_BACKEND => '管理员',
            self::TYPE_DEVELOP => '开发者',
        ];

        return kv($desc, $key, $check_key);
    }

    /**
     * 获取定义的 kv 值
     * @param null|string $key 需要获取的key, 默认返回整个定义
     * @param bool $check_key 检测当前key 是否存在
     * @return array|string
     */
    public static function kvRegType($key = null, $check_key = false)
    {
        $desc = [
            self::REG_TYPE_USERNAME => '用户名',
            self::REG_TYPE_MOBILE   => '手机号',
            self::REG_TYPE_EMAIL    => '邮箱',
        ];

        return kv($desc, $key, $check_key);
    }

    /**
     * 注册平台
     * @param null $key key
     * @param bool $check_exists 检测当前key 是否存在
     * @return array|string
     */
    public static function kvPlatform($key = null, $check_exists = false)
    {
        $desc = [
            self::REG_PLATFORM_ANDROID => 'android',
            self::REG_PLATFORM_IOS     => 'ios',
            self::REG_PLATFORM_PC      => 'pc',
            self::REG_PLATFORM_WEB     => 'web',
            self::REG_PLATFORM_H5      => 'h5',
            self::REG_PLATFORM_WEAPP   => 'weapp',
            self::REG_PLATFORM_WEBAPP  => 'webapp',
            self::REG_PLATFORM_MGRAPP  => 'mgrapp',
        ];

        return kv($desc, $key, $check_exists);
    }

    /**
     * 获取账户实例
     * @return PamAccount
     */
    public static function instance(): PamAccount
    {
        if (config('poppy.core.rbac.account')) {
            $pamClass = config('poppy.core.rbac.account');
            return new $pamClass();
        }
        else {
            return new self();
        }
    }

    /**
     * 默认手机号(国际版默认)
     * @param $id
     * @return string
     */
    public static function dftMobile($id): string
    {
        return '33023-' . sprintf("%s%'.07d", '', $id);
    }
}