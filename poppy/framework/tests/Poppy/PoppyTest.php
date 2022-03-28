<?php

namespace Poppy\Framework\Tests\Poppy;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Poppy\Framework\Application\TestCase;
use Poppy\Framework\Helper\ArrayHelper;

class PoppyTest extends TestCase
{
    /**
     * namespace test
     */
    public function testNamespace(): void
    {
        $namespace = poppy_class('module.site', 'ServiceProvider');
        $this->assertEquals('Site\ServiceProvider', $namespace);
        $namespace = poppy_class('module.site');
        $this->assertEquals('Site', $namespace);
        $namespace = poppy_class('poppy.system', 'ServiceProvider');
        $this->assertEquals('Poppy\System\ServiceProvider', $namespace);
        $namespace = poppy_class('poppy.system');
        $this->assertEquals('Poppy\System', $namespace);
        $namespace = poppy_class('poppy.un_exist');
        $this->assertEquals('', $namespace);
    }


    public function testPath()
    {
        $path = poppy_path('module.site', 'src/models/Default.php');
        $this->assertTrue(Str::endsWith($path, 'modules/site/src/models/Default.php'));
    }

    public function testGenKey(): void
    {
        $arr    = [
            'location' => 'http://www.baidu.com',
            'status'   => 'error',
        ];
        $genKey = ArrayHelper::genKey($arr);

        // 组合数组
        $this->assertEquals('location|http://www.baidu.com;status|error', $genKey);

        // 组合空
        $this->assertEquals('', ArrayHelper::genKey([]));
    }

    public function testAll()
    {
        $this->testOptimize();

        /** @var Collection $enabled */
        $enabled = app('poppy')->all();
        $this->assertNotEquals(0, $enabled->count());
    }

    public function testEnabled()
    {
        $this->testOptimize();

        $enabled = app('poppy')->enabled();
        $this->assertGreaterThan(0, $enabled->count());
    }

    public function testOptimize(): void
    {
        $poppyJson = storage_path('app/poppy.json');
        if (app('files')->exists($poppyJson)) {
            app('files')->delete($poppyJson);
        }
        app('poppy')->optimize();
        $this->assertFileExists($poppyJson);
    }

    /**
     * 测试模块加载
     */
    public function testLoaded(): void
    {
        $folders = glob(base_path('modules/*/src'), GLOB_BRACE);
        collect($folders)->each(function ($folder) {
            $matched = preg_match('/modules\/(?<module>[a-z]*)\/src/', $folder, $matches);
            $name    = 'module.' . $matches['module'];
            if ($matched && !app('poppy')->exists($name)) {
                $this->fail("Module `{$matches['module']}` Not Exist , Please run `php artisan poppy:optimize` to fix.");
            }
            else {
                $this->assertTrue(true, "Module `{$matches['module']}` loaded.");
            }
        });
    }
}