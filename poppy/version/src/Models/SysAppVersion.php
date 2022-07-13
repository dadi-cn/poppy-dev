<?php

declare(strict_types=1);

namespace Poppy\Version\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Poppy\Core\Redis\RdsDb;
use Poppy\System\Classes\Uploader\Uploader;
use Poppy\Version\Classes\PyVersionDef;

/**
 * User\Models\AppVersion
 *
 * @property int $id
 * @property string $title        版本号
 * @property string $description  描述
 * @property string $download_url 下载地址
 * @property int $is_upgrade   是否强制升级当前版本
 * @property string $platform     操作平台 android ios
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|SysAppVersion newModelQuery()
 * @method static Builder|SysAppVersion newQuery()
 * @method static Builder|SysAppVersion query()
 */
class SysAppVersion extends Model
{
    /* 操作平台
   * ---------------------------------------- */
    public const PLATFORM_ANDROID = 'android';
    public const PLATFORM_IOS     = 'ios';

    protected $table = 'sys_app_version';

    protected $fillable = [
        'title',
        'description',
        'download_url',
        'is_upgrade',
        'platform',
    ];

    /**
     * @param null|string $key
     * @param bool $check_key
     * @return array|string
     */
    public static function kvType(string $key = null, bool $check_key = false)
    {
        $desc = [
            self::PLATFORM_ANDROID => '安卓',
            self::PLATFORM_IOS     => 'IOS',
        ];

        return kv($desc, $key, $check_key);
    }

    /**
     * 返回版本
     * @param string $platform 操作平台
     * @return string|array
     */
    public static function latestVersion(string $platform = self::PLATFORM_ANDROID)
    {
        $version = RdsDb::instance()->hGet(PyVersionDef::ckTagMaxVersion(), $platform);
        if (!$version) {
            $versions = self::versions($platform);
            if (count($versions)) {
                $version = array_pop($versions);
            }
            else {
                $version = [
                    'title'       => '1.0.0',
                    'description' => '默认版本',
                ];
            }
            RdsDb::instance()->hSet(PyVersionDef::ckTagMaxVersion(), $platform, $version);
        }
        return $version;
    }

    /**
     * 是否进行强制更新
     * @param string $platform 操作平台
     * @param string $version
     * @return bool
     */
    public static function isUpgrade(string $platform, string $version): bool
    {
        $versions = RdsDb::instance()->hGet(PyVersionDef::ckTagVersions(), $platform);
        if (!$versions) {
            $versions = self::versions($platform);
            RdsDb::instance()->hSet(PyVersionDef::ckTagVersions(), $platform, $versions);
        }
        $isUpgrade = false;
        if (count($versions)) {
            foreach ($versions as $ver) {
                if ($isUpgrade) {
                    continue;
                }
                if (version_compare($ver['title'], $version, '>') && $ver['is_upgrade']) {
                    $isUpgrade = true;
                }
            }
        }
        return $isUpgrade;
    }

    /**
     * 存储的路径
     * @param string $type 类型
     * @return string
     */
    public static function path(string $type = self::PLATFORM_ANDROID): string
    {
        if ($type === self::PLATFORM_ANDROID) {
            $extension = 'apk';
        }
        else {
            $extension = 'ipa';
        }
        return trim(sys_setting('py-version::setting.path', 'static/app/'), '/') . '/' .
            trim(sys_setting('py-version::setting.latest_name', 'latest')) . '.' . $extension;
    }

    /**
     * 平台 Url
     * @param string $type 类型
     * @return mixed|string
     */
    public static function platformUrl(string $type = self::PLATFORM_ANDROID)
    {
        if (
            // android
            $type === self::PLATFORM_ANDROID
            ||
            // 开发状态下的IOS
            ($type === self::PLATFORM_IOS && !sys_setting('py-version:setting.ios_is_prod'))
        ) {
            return Uploader::prefix() . self::path($type);
        }
        return sys_setting('py-version:setting.ios_store_url');
    }

    /**
     * 获取数据库的版本
     * @param string $platform
     * @return array
     */
    protected static function versions(string $platform = self::PLATFORM_ANDROID): array
    {
        $versions = self::query()->where('platform', $platform)->get();
        if ($versions->count()) {
            $arrVersions = $versions->toArray();
            usort($arrVersions, function ($v1, $v2) {
                return version_compare($v1['title'], $v2['title']);
            });
            return $arrVersions;
        }
        return [];
    }
}
