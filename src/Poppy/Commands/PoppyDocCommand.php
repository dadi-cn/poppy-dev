<?php namespace Poppy\Framework\Poppy\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Poppy\Framework\Helper\FileHelper;
use Symfony\Component\Console\Input\InputArgument;

class PoppyDocCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'poppy:doc';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'For Helper Document';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$type = $this->argument('type');
		switch ($type) {
			case 'phpcs';
			case 'cs';
				$this->info(
					'Please Run Command:' . "\n" .
					'php-cs-fixer fix --config=' . pf_path('.php_cs') . ' --diff --dry-run --verbose --diff-format=udiff'
				);
				break;
			case 'phplint':
			case 'lint':
				$lintFile = base_path('vendor/bin/phplint');
				if (file_exists($lintFile)) {
					$this->info('Please Run Command:' . "\n" .
						'./vendor/bin/phplint -c ' . pf_path('.phplint.yml')
					);
				}
				else {
					$this->warn('Please run `composer require overtrue/phplint -vvv` to install phplint');
				}
				break;
			case 'php':
			case 'sami':
				$sami       = storage_path('sami/sami.phar');
				$samiConfig = storage_path('sami/config.php');
				if (!file_exists($samiConfig)) {
					$this->warn('Please Run Command To Publish Config:' . "\n" .
						'php artisan vendor:publish '
					);
					return;
				}
				if (file_exists($sami)) {
					$this->info('Please Run Command:' . "\n" .
						'php ' . $sami . ' update ' . $samiConfig
					);
				}
				else {
					$this->warn('Please Run Command To Install Sami.phar:' . "\n" .
						'curl http://get.sensiolabs.org/sami.phar --output ' . $sami
					);
				}
				break;
			case 'poppy':
			case 'project':
				$aimFolder = public_path('docs/poppy');
				// project readme
				$this->getFile()->copy(base_path('README.md'), $aimFolder . '/README.md');

				// framework docsify
				$this->getFile()->copyDirectory(pf_path('resources/docsify/'), $aimFolder);

				// framework document
				$this->getFile()->copyDirectory(pf_path('docs/'), $aimFolder);

				// modules
				$this->modulesDoc();

				// extension doc
				$this->extensionDoc();

				// project common
				$this->getFile()->copyDirectory(base_path('resources/docs'), $aimFolder);
				$this->info('Publish Success!');
				break;
			default:
				$this->comment('Type is now allowed.');
				break;
		}


	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['type', InputArgument::REQUIRED, ' Support Type [phpcs,cs|php-cs-fixer].'],
		];
	}


	private function modulesDoc()
	{
		$aimFolder = public_path('docs/poppy');

		// append modules doc
		$folders = $this->getFile()->directories(base_path('modules/'));
		if (is_array($folders)) {
			foreach ($folders as $folder) {
				$baseFolder = basename($folder);
				$glob       = glob($folder . '/docs/*.md');
				if (!count($glob)) {
					continue;
				}
				foreach ($glob as $file) {
					$baseFilename = basename($file);
					$fileName     = $baseFolder . '-' . $baseFilename;
					$display      = $baseFolder . '/' . FileHelper::removeExtension($baseFilename);

					// make directory
					$folder = $aimFolder . '/' . $baseFolder . '/';
					if (!$this->getFile()->exists($folder)) {
						$this->getFile()->makeDirectory($folder, 0755, true);
					}

					// 复制文件
					$this->getFile()->copy($file, $folder . $fileName);

					// 追加链接
					$sidebarPath = $aimFolder . '/_sidebar.md';
					if (file_exists($sidebarPath)) {
						$linkText = "\n- [{$display}]({$baseFolder}/{$fileName})";
						$this->getFile()->append($sidebarPath, $linkText);
					}
				}
			}
		}
	}

	private function extensionDoc()
	{
		$aimFolder = public_path('docs/poppy');
		// append extension link
		$folders    = $this->getFile()->directories(base_path('extensions/'));
		$readmePath = '';
		if (is_array($folders)) {
			foreach ($folders as $folder) {
				$baseFolder = basename($folder);
				$glob       = glob($folder . '/[Rr][Ee][Aa][Dd][Mm][Ee].md');
				if (count($glob)) {
					$readmePath = current($glob);
				}
				if ($readmePath) {
					$fileName = $baseFolder . '-' . 'readme.md';
					$display  = $baseFolder . '/Readme';

					// make directory
					$folder = $aimFolder . '/extensions/';
					if (!$this->getFile()->exists($folder)) {
						$this->getFile()->makeDirectory($folder, 0755, true);
					}

					// 复制文件
					$this->getFile()->copy($readmePath, $folder . $fileName);

					// 追加链接
					$sidebarPath = $aimFolder . '/_sidebar.md';
					if (file_exists($sidebarPath)) {
						$linkText = "\n- [{$display}](extensions/{$fileName})";
						$this->getFile()->append($sidebarPath, $linkText);
					}
				}
			}
		}
	}


	/**
	 * @return Filesystem
	 */
	private function getFile()
	{
		return app('files');
	}
}
