<?php

namespace Poppy\System\Tests\Services;

use Poppy\Core\Classes\PyCoreDef;
use Poppy\Framework\Application\TestCase;

class ServicesTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        sys_cache('py-core')->forget(PyCoreDef::ckModule('hook'));
        sys_cache('py-core')->forget(PyCoreDef::ckModule('module'));
    }

    public function testUploadType()
    {
        $uploadTypes = sys_hook('poppy.system.upload_type');
        self::assertArrayHasKey('default', $uploadTypes);
    }
}