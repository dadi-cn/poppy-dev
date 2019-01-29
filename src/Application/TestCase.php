<?php namespace Poppy\Framework\Application;

/**
 * Main Test Case
 */
class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
	/**
	 * Creates the application.
	 */
	public function createApplication()
	{
		$file         = __DIR__ . '/../../../storage/bootstrap/app.php';
		$fileInVendor = __DIR__ . '/../../../../../storage/bootstrap/app.php';
		if (file_exists($file)) {
			$app = require_once $file;
		}
		elseif (file_exists($fileInVendor)) {
			$app = require_once $fileInVendor;
		}

		if ($app !== null) {
			$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

			return $app;
		}
	}

	/**
	 * Run Vendor Test
	 * @param array $vendors test here is must class
	 */
	public function poppyTestVendor(array $vendors = [])
	{
		collect($vendors)->each(function ($class, $package) {
			$this->assertTrue(class_exists($class), "Class `{$class}` is not exist, run `composer require {$package}` to install");
		});
	}
}
