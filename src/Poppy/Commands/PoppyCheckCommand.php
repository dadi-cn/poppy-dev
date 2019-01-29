<?php namespace Poppy\Framework\Poppy\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\SplFileInfo;

class PoppyCheckCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'poppy:check';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Check Name In Rule!';

	private $rules = [];

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$baseDir  = base_path();
		$folders  = glob($baseDir . '/{modules}/*/src/{events,listeners}', GLOB_BRACE);
		$iterator = \Symfony\Component\Finder\Finder::create()
			->files()
			->name('*.php')
			->in($folders);
		foreach ($iterator as $file) {
			$this->check($file);
		}
		$this->table([
			'module' => 'Module', 'file' => 'FileName', 'path' => 'Path',
		], $this->rules);
	}

	protected function check(SplFileInfo $file)
	{
		$pathName = $file->getPathName();
		$fileName = $file->getFileName();
		$module   = function ($str) {
			if (preg_match('/modules\/(.+)\/src/', $str, $match)) {
				return $match[1];
			}
		};
		if (strpos($pathName, '/events/') !== false) {
			if (substr(pathinfo($fileName)['filename'], -5) != 'Event') {
				$this->rules[] = [
					'module' => $module($pathName),
					'file'   => $fileName,
					'path'   => $pathName,
				];
			}
		}
		if (strpos($pathName, '/listeners/') !== false) {
			if (substr(pathinfo($fileName)['filename'], -8) != 'Listener') {
				$this->rules[] = [
					'module' => $module($pathName),
					'file'   => $fileName,
					'path'   => $pathName,
				];
			}
		}
	}
}
