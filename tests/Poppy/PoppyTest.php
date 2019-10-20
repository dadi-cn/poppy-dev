<?php namespace Poppy\Framework\Tests\Poppy;

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

		$enabled = app('poppy')->all();
		dd($enabled);
	}

	public function testEnabled()
	{
		$this->testOptimize();

		$enabled = app('poppy')->enabled();
		dd($enabled);
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
			if ($matched && !app('poppy')->exists($matches['module'])) {
				$this->assertTrue(false, "Module `{$matches['module']}` Not Exist , Please run `php artisan poppy:optimize` to fix.");
			}
			else {
				$this->assertTrue(true, "Module `{$matches['module']}` loaded.");
			}
		});
	}
}