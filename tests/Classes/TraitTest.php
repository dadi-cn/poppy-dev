<?php namespace Poppy\Framework\Tests\Classes;

use Poppy\Framework\Application\TestCase;
use Poppy\Framework\Classes\Resp;

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
	}
}