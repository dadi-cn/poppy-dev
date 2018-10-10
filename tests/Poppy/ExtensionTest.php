<?php namespace Poppy\Framework\Tests\Poppy;

use Poppy\Framework\Application\TestCase;

class ExtensionTest extends TestCase
{
	public function testExtension()
	{
		dd(app('extension')->repository());
	}
}