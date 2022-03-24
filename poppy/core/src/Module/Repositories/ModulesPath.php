<?php

namespace Poppy\Core\Module\Repositories;

use Illuminate\Support\Collection;
use Poppy\Core\Classes\PyCoreDef;
use Poppy\Core\Exceptions\ModuleException;
use Poppy\Core\Exceptions\PermissionException;
use Poppy\Core\Rbac\Contracts\RbacUserContract;
use Poppy\Core\Rbac\Traits\RbacUserTrait;
use Poppy\Framework\Helper\StrHelper;
use Poppy\Framework\Support\Abstracts\Repository;

/**
 * 模块路径.
 */
class ModulesPath extends Repository
{

    /**
     * Initialize.
     * @param Collection $collection 集合
     */
    public function initialize(Collection $collection)
    {
        // check serve setting
        $this->items = sys_cache('py-core')->remember(
            PyCoreDef::ckModule('path'),
            PyCoreDef::MIN_ONE_DAY * 60,
            function () use ($collection) {
                $collect = collect();
                $collection->each(function ($definition, $slug) use ($collect) {
                    // slug  - poppy.mgr-app
                    collect($definition)->each(function ($menus, $type) use ($slug, $collect) {
                        // $type - backend/develop/web
                        collect($menus)->each(function ($menu, $key) use ($type, $slug, $collect) {
                            // $key  : setting
                            if (!is_array($menu)) {
                                throw new ModuleException("Module `{$slug}`'s key `{$key}` on `{$type}` is not an array");
                            }

                            $conf             = app('poppy')->where('slug', $slug);
                            $menu['enabled']  = $conf['enabled'] ?? false;
                            $menu['order']    = $conf['order'] ?? 0;
                            $menu['type']     = $type;
                            $menu['children'] = $this->parseLink($menu['children'], $slug);
                            $collect->put("{$slug}/{$type}||{$key}", $menu);
                        });
                    });
                });
                $collect->sortBy('order', SORT_ASC);

                /* 对 Injection 进行处理
                 * ---------------------------------------- */
                $reCollection = collect($collect->toArray());

                $collect->each(function ($definition, $key) use ($reCollection) {

                    $injection = $definition['injection'] ?? '';

                    if (!$injection) {
                        return;
                    }
                    if ($reCollection->offsetExists($injection)) {
                        $item             = $reCollection->get($injection);
                        $item['children'] = array_merge($item['children'], $definition['children']);
                        $reCollection->put($injection, $item);
                        $reCollection->offsetUnset($key);
                    }
                });
                return $reCollection->all();
            }
        );
    }

    /**
     * 根据用户返回合适的菜单
     * @param string $type 指定用户的类型
     * @param bool $is_full_permission 是否是全部权限
     * @param null|RbacUserTrait|RbacUserContract $pam 用户
     * @return Collection
     * @throws PermissionException
     */
    public function withPermission(string $type, bool $is_full_permission = false, $pam = null): Collection
    {
        $navs = $this->where('type', $type);

        if (!$is_full_permission && is_null($pam)) {
            throw new PermissionException('非全部权限用户需要用户实体设定');
        }

        $collect = collect();
        $navs->each(function ($nav, $key) use ($pam, $collect, $is_full_permission) {
            $newMenu = collect();
            collect($nav['children'])->each(function ($menu) use ($newMenu, $pam, $is_full_permission) {

                /* 三级菜单的权限
                 * ---------------------------------------- */
                if ($menu['children'] ?? []) {
                    $newSubmenus = collect();
                    collect($menu['children'] ?? [])->each(function ($submenu) use ($newSubmenus, $pam, $is_full_permission) {
                        if ($submenu['permission'] ?? '') {
                            // 管理员拥有所有权限
                            if ($is_full_permission || $pam->capable($submenu['permission'])) {
                                unset($submenu['permission'], $submenu['route']);
                                $newSubmenus->push($submenu);
                            }
                        } else {
                            unset($submenu['route']);
                            $newSubmenus->push($submenu);
                        }
                    });
                    if ($newSubmenus->count()) {
                        $menu['children'] = $newSubmenus->toArray();
                        $newMenu->push($menu);
                    }
                }

                if ($menu['route'] ?? '') {
                    if ($menu['permission'] ?? '') {
                        // 管理员拥有所有权限
                        if ($is_full_permission || $pam->capable($menu['permission'])) {
                            unset($menu['permission'], $menu['route']);
                            $newMenu->push($menu);
                        }
                    } else {
                        unset($menu['route']);
                        $newMenu->push($menu);
                    }
                }

            });
            $nav['children'] = $newMenu;
            $collect->put($key, $nav);
        });

        return $collect;
    }

    /**
     * @param string $type 类型
     * @param array $perms perms
     * @return Collection
     */
    public function withType(string $type, array $perms): Collection
    {
        $menus = $this->where('type', $type);
        $menu  = collect();
        $menus->each(function ($module) use ($menu, $perms) {
            $groups = collect();
            collect($module)->each(function ($group) use ($groups, $perms) {
                $children = collect();
                collect($group['children'])->each(function ($url) use ($children, $perms) {
                    if (isset($url['permission']) && $url['permission']) {
                        if (in_array($url['permission'], $perms, true)) {
                            $children->push($url);
                        }
                    } else {
                        $children->push($url);
                    }
                });
                $group['children'] = $children;
            });
            $module = $groups;
            if (count($module)) {
                $menu->push($module);
            }
        });

        return $menu;
    }

    public static function parse($path): array
    {
        $mt     = explode('/', $path);
        $type   = $mt[0];
        $route  = $mt[1];
        $params = explode(',', $mt[2] ?? '');
        $query  = StrHelper::parseKey($mt[3] ?? '');

        return [
            'path'  => route($route, $params, false),
            'query' => $query,
            'type'  => $type,
        ];
    }

    /**
     * 解析链接
     * @param array $submenus 数据数组
     * @return array
     * @throws ModuleException
     */
    private function parseLink(array $submenus, string $slug): ?array
    {
        foreach ($submenus as &$submenu) {
            if (isset($submenu['children']) && is_array($submenu['children'])) {
                // parse children
                $submenu['children'] = $this->parseLink($submenu['children'], $slug);
            } else {

                if (!isset($submenu['title'])) {
                    throw new ModuleException("Error define at module path {$slug}");
                }
                if (!isset($submenu['path'])) {
                    throw new ModuleException("Error define path at `{$slug}` on {$submenu['title']}");
                }
                $mt = explode('/', $submenu['path']);
                if (count($mt) < 2) {
                    throw new ModuleException("Error define path at `{$slug}` on {$submenu['title']}, must contain type and route");
                }
                $route     = $mt[1];
                $routeHide = (array) config('poppy.core.route_hide');
                if (in_array($route, $routeHide, false)) {
                    return null;
                }
                $submenu = array_merge($submenu, self::parse($submenu['path']));
            }
        }

        return $submenus;
    }
}
