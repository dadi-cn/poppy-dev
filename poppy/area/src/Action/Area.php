<?php

namespace Poppy\Area\Action;

use Exception;
use Illuminate\Contracts\View\Factory;
use Poppy\Area\Classes\PyAreaDef;
use Poppy\Area\Models\AreaContent;
use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\Framework\Validation\Rule;
use Poppy\System\Classes\Traits\FixTrait;
use Poppy\System\Classes\Traits\PamTrait;
use Throwable;
use Validator;
use View;

/**
 * 地区
 * @url https://segmentfault.com/a/1190000021127798
 */
class Area
{
    use AppTrait, PamTrait, FixTrait;

    /**
     * @var AreaContent
     */
    protected $area;

    /**
     * @var int AreaContent id
     */
    protected $areaId;

    /**
     * @var string
     */
    protected $areaTable;

    public function __construct()
    {
        $this->areaTable = (new AreaContent())->getTable();
    }

    /**
     * 创建需求
     * @param array    $data 创建数据
     *                       string  title       标题
     *                       int     parent_id   父id
     *                       int     top_id      顶级id
     * @param null|int $id   地区id
     * @return bool
     */
    public function establish(array $data, $id = null): bool
    {
        if (!$this->checkPam()) {
            return false;
        }

        $initDb    = [
            'title'     => trim((string) data_get($data, 'title', '')),
            'parent_id' => (int) data_get($data, 'parent_id', 0),
        ];
        $validator = Validator::make($initDb, [
            'title'     => [
                Rule::required(),
                Rule::string(),
            ],
            'parent_id' => [
                Rule::required(),
                Rule::integer(),
            ],
        ], [], [
            'title'     => sys_db('area_content.title'),
            'parent_id' => sys_db('area_content.parent_id'),
        ]);
        if ($validator->fails()) {
            return $this->setError($validator->messages());
        }
        if ($id && !$this->initArea($id)) {
            return false;
        }
        if (!$initDb['parent_id'] && $data['top_id']) {
            $initDb['parent_id'] = (int) $data['top_id'];
        }
        $needUpdate = [];
        $this->matchKv(true);
        if ($id) {
            if ($id === $initDb['parent_id']) {
                return $this->setError(trans('py-system::action.area.same_error'));
            }
            $needUpdate = array_merge(
                $needUpdate,
                $this->parentIds($this->areaId, 'array'),
                [$this->areaId]
            );
            $this->area->update($initDb);
        }
        else {
            $area       = AreaContent::create($initDb);
            $this->area = $area;
        }

        $this->matchKv(true);

        $needUpdate = array_merge(
            $needUpdate,
            $this->parentIds($this->area->id, 'array'),
            [$this->area->id]
        );

        $this->batchFix(array_unique($needUpdate));

        return true;
    }

    /**
     * 删除数据
     * @param int $id 地区id
     * @return bool|null
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        if ($id && !$this->initArea($id)) {
            return false;
        }
        if (AreaContent::where('parent_id', $id)->exists()) {
            return $this->setError(trans('py-system::action.area.exist_error'));
        }
        $parentIds = $this->parentIds($id, 'array');
        $this->area->delete();
        $this->batchFix($parentIds);

        return true;
    }

    /**
     * 获取父元素IDs
     * @param int    $id   地区id
     * @param string $type 类型
     * @return string|array
     */
    public function parentIds(int $id, $type = 'string')
    {
        $matchKv = $this->matchKv();
        $ids     = [];
        while (isset($matchKv[$id])) {
            $id    = $matchKv[$id];
            $ids[] = $id;
        }
        $ids = array_reverse($ids);

        return ($type == 'string') ? implode(',', $ids) : $ids;
    }

    /**
     * 获取所有的子id
     * @param int|array $id 地区id
     * @return array
     */
    public function getChildren($id): array
    {
        $matchKv = $this->matchKv();
        if (!is_array($id)) {
            $ids = [$id];
        }
        else {
            $ids = $id;
        }

        $children = [];
        foreach ($ids as $_id) {
            $children = array_merge($children, array_keys($matchKv, $_id));
        }

        if (!$children) {
            return $ids;
        }

        return array_merge($ids, $this->getChildren($children));
    }

    /**
     * 修复分类代码
     * @param int $id 地区id
     */
    public function fix(int $id)
    {
        $children    = $this->getChildren($id);
        $topParentId = $this->topParentId($id);
        AreaContent::where('id', $id)->update([
            'children'      => implode(',', $children),
            'top_parent_id' => $topParentId,
        ]);
    }

    /**
     * 修复分类代码的处理
     * @return Factory|\Illuminate\View\View
     */
    public function fixHandle()
    {
        $this->fixInit();
        // 重新清理掉缓存
        if (!$this->fix['cached']) {
            $this->fix['cached'] = 1;
        }
        $Db = AreaContent::whereRaw('1=1');
        if (!$this->fix['total']) {
            $this->fix['total'] = $Db->count();
        }
        if (!$this->fix['max']) {
            $this->fix['max'] = $Db->max('id');
        }
        if (!$this->fix['min']) {
            $this->fix['min'] = $Db->min('id');
        }

        // ↑↑↑↑↑↑↑↑↑↑↑   获取参数

        // 剩余数
        $this->fix['left'] = $Db->whereRaw('id > ?', [$this->fix['start']])
            ->count('id');

        $this->fix['lastId'] = $this->fix['start'];
        if ($this->fix['left']) {
            $left_items = AreaContent::whereRaw('id >= ?', [$this->fix['start']])
                ->take($this->fix['section'])
                ->orderBy('id')
                ->get(['id', 'title']);

            foreach ($left_items as $item) {
                (new self())->fix($item->id);
                // hasChild 子集 level 等级
                (new self())->hasChild($item->id);
                (new self())->level($item->id);
                $this->fix['lastId'] = $item->id + 1;
            }
        }

        return $this->fixView();
    }

    /**
     * 判断是否有子集
     * @param int $id 地区id
     * @return bool
     */
    public function hasChild(int $id): bool
    {
        $parent = AreaContent::where('id', $id)->value('parent_id');
        if (AreaContent::where('id', $id)->where('top_parent_id', $id)->exists() || AreaContent::where('id', $id)->where('top_parent_id', $parent)->exists()) {
            AreaContent::where('id', $id)->update([
                'has_child' => 1,
            ]);
        }
        else {
            AreaContent::where('id', $id)->update([
                'has_child' => 0,
            ]);
        }

        return true;
    }

    /**
     * 等级
     * @param int $id 地区id
     * @return bool
     */
    public function level(int $id): bool
    {
        //省级
        AreaContent::where('id', $id)->where('parent_id', 0)->update([
            'level' => 1,
        ]);
        // 市级
        $parent = AreaContent::where('id', $id)->value('parent_id');
        if (AreaContent::where('id', $parent)->where('parent_id', 0)->exists()) {
            AreaContent::where('id', $id)->update([
                'level' => 2,
            ]);
        }

        return true;
    }

    /**
     * 初始化id
     * @param int $id 地区Id
     * @return bool
     */
    public function initArea(int $id): bool
    {
        try {
            $this->area   = AreaContent::findOrFail($id);
            $this->areaId = $this->area->id;

            return true;
        } catch (Throwable $e) {
            return $this->setError(trans('py-area::action.area.undefined_error'));
        }
    }

    /**
     * 共享变量
     */
    public function share()
    {
        View::share([
            'item' => $this->area,
            'id'   => $this->area->id,
        ]);
    }

    /**
     * @param bool $clear 是否清除
     * @return mixed
     */
    private function matchKv($clear = false)
    {
        if ($clear) {
            sys_cache('py-area')->forget(PyAreaDef::ckMatchIdPid());
        }

        return sys_cache('py-area')->remember(PyAreaDef::ckMatchIdPid(), 10, function () {
            return AreaContent::pluck('parent_id', 'id')->toArray();
        });
    }

    /**
     * @param array $ids 地区id列表
     */
    private function batchFix(array $ids)
    {
        foreach ($ids as $id) {
            if (!$id) {
                continue;
            }
            $this->fix($id);
        }
    }

    /**
     * @param int $id 顶级id
     * @return mixed
     */
    private function topParentId(int $id)
    {
        $parentIds = $this->parentIds($id, 'array');
        if (count($parentIds) == 1) {
            return $id;
        }

        return $parentIds[1];
    }
}