<?php namespace DummyNamespace\Http\Request\Backend;

use Poppy\System\Http\Request\Backend\BackendController;

class DemoController extends BackendController
{
	public function index()
	{
		return 'DummyNamespace Backend Request Success';
	}
}