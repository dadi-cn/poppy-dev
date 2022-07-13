<?php

namespace Poppy\Version\Tests\Models;

use DB;
use Exception;
use Poppy\Core\Redis\RdsDb;
use Poppy\System\Tests\Base\SystemTestCase;
use Poppy\Version\Classes\PyVersionDef;
use Poppy\Version\Models\SysAppVersion;

class SysAppVersionTest extends SystemTestCase
{

    protected $enableDb = true;


    public function setUp():void
    {
        parent::setUp();
        RdsDb::instance()->del(PyVersionDef::ckTagMaxVersion());
        RdsDb::instance()->del(PyVersionDef::ckTagVersions());
    }

    /**
     * 最新版本号
     * @throws Exception
     */
    public function testVersion(): void
    {
        $version = SysAppVersion::latestVersion();
        $this->assertIsArray($version);
    }

    /**
     * @throws Exception
     */
    public function testIsUpgrade()
    {
        $versions = [
            [
                'title'      => '4.4.4',
                'is_upgrade' => 0,
                'platform'   => SysAppVersion::PLATFORM_ANDROID,
            ],
            [
                'title'      => '4.5.0',
                'is_upgrade' => 1,
                'platform'   => SysAppVersion::PLATFORM_ANDROID,
            ],
            [
                'title'      => '4.6.0',
                'is_upgrade' => 0,
                'platform'   => SysAppVersion::PLATFORM_ANDROID,
            ],
        ];
        SysAppVersion::query()->insert($versions);
        $isUpgrade445 = SysAppVersion::isUpgrade(SysAppVersion::PLATFORM_ANDROID, '4.4.5');
        $this->assertTrue($isUpgrade445);

        $isUpgrade451 = SysAppVersion::isUpgrade(SysAppVersion::PLATFORM_ANDROID, '4.5.1');
        $this->assertFalse($isUpgrade451);
    }
}