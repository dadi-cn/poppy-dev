<?php namespace DummyNamespace\Http\Request\Web;

use Poppy\Framework\Application\Controller;

class DemoController extends Controller
{
	public function index()
	{
		return 'DummyNamespace Web Request Success';
	}
}