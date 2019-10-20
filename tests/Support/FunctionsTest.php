<?php namespace Poppy\Framework\Tests\Support;

use Poppy\Framework\Application\TestCase;

class FunctionsTest extends TestCase
{

	public function testPoppyPath(): void
	{
		// module - system
		$systemPath = poppy_path('system', 'src/sample.php');
		$this->assertEquals(base_path('modules/system/src/sample.php'), $systemPath);

		$systemPath = poppy_path('module.system', 'src/sample.php');
		$this->assertEquals(base_path('modules/system/src/sample.php'), $systemPath);

		// poppy - system
		$poppySystemPath = poppy_path('poppy.system', 'src/sample.php');
		$this->assertEquals(app('path.poppy') . '/system/src/sample.php', $poppySystemPath);
	}
}