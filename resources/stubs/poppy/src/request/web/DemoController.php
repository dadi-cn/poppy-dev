<?php namespace DummyNamespace\Request\Web;

use Poppy\Framework\Application\ApiController;

class DemoController extends ApiController
{

	public function index()
	{
		return 'DummyNamespace Web Request Success';
	}
}