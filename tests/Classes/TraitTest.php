<?php namespace Poppy\Framework\Tests\Classes;

use Poppy\Framework\Application\TestCase;
use Poppy\Framework\Classes\Resp;
use Poppy\System\Module\Module;

class TraitTest extends TestCase
{
	public function testApp(): void
	{
		$class = new TraitDemo();
		$class->error();
		$this->assertEquals(Resp::ERROR, $class->getError()->getCode());

		$class->exception();
		$this->assertEquals(Resp::ERROR, $class->getError()->getCode());

		$class->exceptionWithCode(110011);
		$this->assertEquals(110011, $class->getError()->getCode());

		$class->success();
		$this->assertEquals(Resp::SUCCESS, $class->getSuccess()->getCode());

		$class->successWithEmpty();
		$this->assertEquals(Resp::SUCCESS, $class->getSuccess()->getCode());

		$class->successWithEmpty();
		$this->assertNotEquals('', $class->getSuccess()->getMessage());
	}

	public function testHasAttributes()
	{
		$module = (new Module('module.site'));

		$this->assertEquals(base_path('modules/site'), $module->directory());

		$this->assertEquals('module.site', $module->slug());

		$this->assertEquals('Site', $module->namespace());
	}

	public function testKeyParser()
	{
		$class = new TraitDemo();
		[$n, $g, $k] = $class->parseKey('n::g.k');

		$this->assertEquals('g', $g);
		$this->assertEquals('n', $n);
		$this->assertEquals('k', $k);
	}
}