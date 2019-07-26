<?php namespace DummyNamespace\Http\Request\Backend;

use System\Http\Request\Backend\BackendController;

class DemoController extends BackendController
{
	public function index()
	{
		return 'DummyNamespace Backend Request Success';
	}
}