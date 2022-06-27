<?php

namespace Poppy\Area\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Poppy\Area\Classes\PyAreaDef;
use Poppy\Core\Classes\PyCoreDef;
use Poppy\Framework\Helper\TreeHelper;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\Framework\Http\Pagination\PageInfo;
use Poppy\System\Classes\Traits\FilterTrait;

/**
 * 地区表
 *
 * @property int $id
 * @property string $code 编码
 * @property string $title 名称
 * @property string $parent_id       父级
 * @property string $top_parent_id   顶层ID
 * @property string $children        所有的子元素
 * @property int $has_child          是否有子元素
 * @property int $level              级别
 * @method static Builder|SysArea filter($input = [], $filter = null)
 * @method static Builder|SysArea pageFilter(PageInfo $pageInfo)
 * @method static Builder|SysArea paginateFilter($perPage = null, $columns = [], $pageName = 'page', $page = null)
 * @method static Builder|SysArea simplePaginateFilter($perPage = null, $columns = [], $pageName = 'page')
 * @method static Builder|SysArea whereBeginsWith($column, $value, $boolean = 'and')
 * @method static Builder|SysArea whereEndsWith($column, $value, $boolean = 'and')
 * @method static Builder|SysArea whereLike($column, $value, $boolean = 'and')
 * @mixin Eloquent
 * @url https://github.com/wecatch/china_regions
 */
class SysArea extends Model
{
    use FilterTrait;

    const LEVEL_PROVINCE = 1;
    const LEVEL_CITY     = 2;
    const LEVEL_COUNTY   = 4;

    public $timestamps = false;

    protected $table = 'sys_area';

    protected $fillable = [
        'title',
        'code',
        'parent_id',
        'has_child',   // 是否有子集
        'level',       // 级别
        'top_parent_id',
        'children',
    ];

    public static function cityTree()
    {
        return sys_cache('py-area')->remember(PyAreaDef::ckArea('tree-level-2'), PyCoreDef::MIN_ONE_MONTH * 60, function () {
            $items = SysArea::selectRaw("id,title,parent_id")->where('level', '<', 4)->get()->keyBy('id')->toArray();
            $Tree  = new TreeHelper();
            $Tree->init($items, 'id', 'parent_id', 'title');
            return $Tree->getTreeArray(0);
        });
    }

    public static function cityMgrTree()
    {
        return sys_cache('py-area')->remember(PyAreaDef::ckArea('tree-level-2-mgr'), PyCoreDef::MIN_ONE_MONTH * 60, function () {
            $items       = SysArea::selectRaw("id,title,parent_id")->where('level', '<', 4)->get()->keyBy('id')->toArray();
            $Tree        = new TreeHelper();
            $Tree->space = ' ';
            $Tree->icon  = [' │', ' ├', ' └'];
            $Tree->init($items, 'id', 'parent_id', 'title');
            $treeArr = $Tree->getTreeArray(0);
            $options = [];
            foreach ($treeArr as $key => $value) {
                $options[] = [
                    'label' => $value,
                    'value' => (string) $key,
                ];
            }
            return $options;
        });
    }

    /**
     * 地区选择
     * @param string $type 类型, 支持 province,city,area
     * @return array
     */
    public static function cascader(string $type = 'province'): array
    {
        $levels = [
            'province' => 1,
            'city'     => 2,
            'area'     => 4,
        ];
        $level  = $levels[$type] ?? 2;
        return sys_cache('py-area')->remember(PyAreaDef::ckArea('cascader-' . $level), PyCoreDef::MIN_ONE_MONTH * 60, function () use ($level) {
            $items = SysArea::selectRaw("id as value,title as label,parent_id, if(level<4, 0, 1) as leaf")->where('level', '<=', $level)->get()->toArray();
            return UtilHelper::genTree($items, 'value', 'parent_id', 'children', false);
        });
    }

    /**
     * 城市的KV
     * @param string $code 4位长度, 匹配身份证省份/城市
     * @return mixed
     */
    public static function kvCity(string $code = '')
    {
        static $cache;
        if (!$cache) {
            $cache = sys_cache('py-area')->remember(PyAreaDef::ckArea('kv-4'), PyCoreDef::MIN_ONE_MONTH * 60, function () {
                return self::where('level', self::LEVEL_CITY)->selectRaw('left(code, 4) as code, title')->pluck('title', 'code')->toArray();
            });
        }
        return kv($cache, $code);
    }


    /**
     * ID : Title
     * @param string $id ID : Title
     * @return mixed
     */
    public static function kvArea(string $id = '')
    {
        static $cache;
        if (!$cache) {
            $cache = sys_cache('py-area')->remember(PyAreaDef::ckArea('kv-area'), PyCoreDef::MIN_ONE_MONTH * 60, function () {
                return self::select(['id', 'title'])->pluck('title', 'id')->toArray();
            });
        }
        return kv($cache, $id);
    }


    /**
     * 省份的KV
     * @param string $code 2位长度, 匹配身份证省份/城市
     * @return mixed
     */
    public static function kvProvince(string $code = '')
    {
        static $cache;
        if (!$cache) {
            $cache = sys_cache('py-area')->remember(PyAreaDef::ckArea('kv-province'), PyCoreDef::MIN_ONE_MONTH * 60, function () {
                return self::where('level', self::LEVEL_PROVINCE)->selectRaw('left(code, 2) as code, title')->pluck('title', 'code')->toArray();
            });
        }
        return kv($cache, $code);
    }

    /**
     * 城市的KV
     * @param int|null $province
     * @return array|mixed
     */
    public static function kvCityId(int $province)
    {
        static $cache;
        if (!$cache) {
            $cache = sys_cache('py-area')->remember(PyAreaDef::ckArea('kv-city-id-' . $province), PyCoreDef::MIN_ONE_MONTH * 60, function () use ($province) {
                return self::where('level', self::LEVEL_CITY)->where('parent_id', $province)->selectRaw('id, title')->pluck('title', 'id')->toArray();
            });
        }
        return $cache;
    }

    /**
     * 省份的KV
     * @param int|null $id 2位长度, 匹配身份证省份/城市
     * @return array|string
     */
    public static function kvProvinceId(int $id = null)
    {
        static $cache;
        if (!$cache) {
            $cache = sys_cache('py-area')->remember(PyAreaDef::ckArea('kv-province-id'), PyCoreDef::MIN_ONE_MONTH * 60, function () {
                return self::where('level', self::LEVEL_PROVINCE)->selectRaw('id, title')->pluck('title', 'id')->toArray();
            });
        }
        return kv($cache, $id);
    }


    /**
     * 国家KV
     * @param string $code
     * @return string|array
     */
    public static function kvCountry($code = null)
    {
        static $cache;
        if (!$cache) {
            $cache = sys_cache('py-area')->remember(PyAreaDef::ckCountry('kv'), PyCoreDef::MIN_ONE_MONTH * 60, function () {
                $area    = self::country();
                $collect = [];
                collect($area)->each(function ($country) use (&$collect) {
                    $collect[$country['iso']] = $country['zh'];
                });
                return $collect;
            });
        }
        return kv($cache, $code);
    }

    /**
     * 国别缓存
     * @return array|mixed
     */
    public static function country()
    {
        return sys_cache('py-area')->remember(PyAreaDef::ckCountry(), PyCoreDef::MIN_ONE_MONTH * 60, function () {
            $values = include poppy_path('poppy.area', 'resources/def/country.php');
            return collect($values)->sortBy('py')->map(function ($cty) {
                $cty['py'] = strtoupper($cty['py']);
                return $cty;
            })->values()->toArray();
        });
    }
}