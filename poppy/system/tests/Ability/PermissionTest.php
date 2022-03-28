<?php

namespace Poppy\System\Tests\Ability;

use Poppy\Core\Classes\Traits\CoreTrait;
use Poppy\Core\Rbac\Permission\Permission;
use Poppy\System\Models\PamPermission;
use Poppy\System\Models\PamRole;
use Poppy\System\Tests\Base\SystemTestCase;

class PermissionTest extends SystemTestCase
{
    use CoreTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->initPam();
    }

    /**
     * 检测权限不为空
     */
    public function testPermissions(): void
    {
        $permissions = $this->corePermission()->permissions();
        $items       = $permissions->map(function (Permission $permission) {
            return [
                $permission->module(),
                $permission->type(),
                $permission->key(),
                $permission->groupTitle(),
            ];
        });

        // $this->table(['module', 'type', 'key', 'title'], $items);

        $this->assertNotEmpty($items);
    }

    /**
     * 检测是否存在指定权限
     */
    public function testHasPermission(): void
    {
        /** @var PamRole $role */
        $role = PamRole::where('name', 'user')->first();


        $key = 'backend:py-system.global.manage';

        /** @var Permission $permission */
        $permission = $this->corePermission()->permissions()->offsetGet($key);


        if ($permission) {
            $dbPerm = PamPermission::where('name', $key)->first();
            if ($role->hasPermission($key)) {
                $role->detachPermission($dbPerm);
            }
            $role->attachPermission($dbPerm);
            $role->save();
            if ($this->pam->capable($permission->key())) {
                $this->assertTrue(true);
            }
            else {
                $this->fail("没有 '{$permission->description()} '权限, 无权操作");
            }
        }
        else {
            $this->fail('Permission Not Exists!');
        }
    }
}
