<?php namespace DummyNamespace\Request\Api;

use Poppy\Framework\Application\ApiController;

class DemoController extends ApiController
{
	public function index()
	{
		return 'DummyNamespace Api Request Success';
	}
}