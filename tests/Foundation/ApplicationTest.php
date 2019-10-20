<?php namespace Poppy\Framework\Tests\Foundation;

use Poppy\Framework\Application\TestCase;

class ApplicationTest extends TestCase
{
	/**
	 * Path
	 */
	public function testPath(): void
	{
		// framework path
		$this->assertEquals(dirname(__FILE__, 3), app('path.framework'));
		$this->assertEquals(framework_path(), app('path.framework'));
		$this->assertEquals(base_path('modules'), app('path.module'));
		$this->assertEquals(home_path(), app('path.poppy'));
	}
}