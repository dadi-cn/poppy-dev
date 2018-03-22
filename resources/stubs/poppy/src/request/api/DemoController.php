<?php namespace DummyNamespace\Request\Api;

use Poppy\Framework\Application\ApiController;
use System\Classes\Traits\SystemTrait;

class DemoController extends ApiController
{
	use SystemTrait;

	public function index()
	{
		return 'DummyNamespace Api Request Success';
	}
}