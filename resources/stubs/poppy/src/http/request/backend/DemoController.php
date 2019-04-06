<?php namespace DummyNamespace\Http\Request\Backend;

use System\Http\Request\Backend\InitController;

class DemoController extends InitController
{
	public function index()
	{
		return 'DummyNamespace Backend Request Success';
	}
}