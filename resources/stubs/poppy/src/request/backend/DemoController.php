<?php namespace DummyNamespace\Request\Backend;

use System\Http\Request\Backend\InitController;

class DemoController extends InitController
{
	public function index()
	{
		return 'DummyNamespace Backend Request Success';
	}
}