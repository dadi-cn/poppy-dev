<?php

namespace Poppy\System\Setting\Repository;

use Exception;
use Illuminate\Support\Str;
use Poppy\Core\Classes\Contracts\SettingContract;
use Poppy\Core\Redis\RdsDb;
use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\Framework\Classes\Traits\KeyParserTrait;
use Poppy\System\Classes\PySystemDef;
use Poppy\System\Models\SysConfig;
use Throwable;

/**
 * system config
 * Setting Repository
 */
class SettingRepository implements SettingContract
{
    use KeyParserTrait, AppTrait;

    /**
     * @var RdsDb
     */
    private static $rds;

    public function __construct()
    {
        if (!self::$rds) {
            self::$rds = RdsDb::instance();
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): bool
    {
        if (!$this->keyParserMatch($key)) {
            return $this->setError(trans('py-system::util.setting.key_not_match', [
                'key' => $key,
            ]));
        }
        $record = $this->findRecord($key);
        if ($record) {
            try {
                self::$rds->hSet(PySystemDef::ckSetting(), $this->convertKey($key), $record->value);
                $record->delete();
            } catch (Exception $e) {
                return false;
            }
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, $default = '')
    {
        if (!$this->keyParserMatch($key)) {
            return $this->setError(trans('py-system::util.setting.key_not_match', [
                'key' => $key,
            ]));
        }

        if ($val = self::$rds->hGet(PySystemDef::ckSetting(), $this->convertKey($key), false)) {
            return unserialize($val);
        }

        $record = $this->findRecord($key);
        if (!$record) {
            $this->set($key, $default);
            return $default;
        }

        self::$rds->hSet(PySystemDef::ckSetting(), $this->convertKey($key), $record->value);

        return unserialize($record->value);
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value = ''): bool
    {
        if (is_array($key)) {
            foreach ($key as $_key => $_value) {
                if (!$this->set($_key, $_value)) {
                    return false;
                }
            }

            return true;
        }

        if (!$this->keyParserMatch($key)) {
            return $this->setError(trans('py-system::util.setting.key_not_match', [
                'key' => $key,
            ]));
        }

        $record = $this->findRecord($key);
        if (!$record) {
            [$namespace, $group, $item] = $this->parseKey($key);
            SysConfig::create([
                'namespace' => $namespace,
                'group'     => $group,
                'item'      => $item,
                'value'     => serialize($value),
            ]);
        } else {
            $record->value = serialize($value);
            $record->save();
        }

        self::$rds->hSet(PySystemDef::ckSetting(), $this->convertKey($key), serialize($value));
        return true;
    }

    /**
     * 根据命名空间从数据库中获取数据
     * @param string $ng 命名空间和分组
     * @return array
     */
    public function getNG(string $ng): array
    {
        [$ns, $group] = explode('::', $ng);
        if (!$ns || !$group) {
            return [];
        }
        $values = SysConfig::where('namespace', $ns)->where('group', $group)->select(['item', 'value'])->get();
        $data   = collect();
        $values->each(function ($item) use ($data) {
            $data->put($item['item'], unserialize($item['value']));
        });

        return $data->toArray();
    }

    /**
     * 删除命名空间以及分组
     * @param string $ng
     * @return bool
     */
    public function removeNG(string $ng): bool
    {
        if (!Str::contains($ng, '::')) {
            return false;
        }
        [$ns, $group] = explode('::', $ng);
        if (!$ns && !$group) {
            return false;
        }
        $Db     = SysConfig::where('namespace', $ns)->where('group', $group);
        $values = (clone $Db)->pluck('item');
        if ($values->count()) {
            $keys = [];
            $values->each(function ($item) use ($ns, $group, &$keys) {
                $keys[] = $this->convertKey("{$ns}::{$group}.{$item}");
            });
            self::$rds->hDel(PySystemDef::ckSetting(), $keys);
            try {
                $Db->delete();
                return true;
            } catch (Throwable $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * 保存配置
     * @deprecated 3.2 直接从缓存中读取
     */
    public function save(): void
    {
        // none
    }

    /**
     * 设置是否重新读取缓存
     * @param bool $reRead 标识
     * @deprecated 3.2 直接从缓存中取, 无视静态变量
     */
    public function setReRead(bool $reRead): void
    {
        return;
    }

    /**
     * 转换 KEY
     * @param $key
     * @return string
     */
    private function convertKey($key): string
    {
        return str_replace(['::', '.'], ['--', '-'], $key);
    }

    /**
     * Returns a record (cached)
     * @param string $key 获取的key
     * @return SysConfig|null
     */
    private function findRecord(string $key): ?SysConfig
    {
        /** @var SysConfig $record */
        $record = SysConfig::query();

        return $record->applyKey($key)->first();
    }
}
