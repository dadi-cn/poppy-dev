<?php namespace Poppy\Framework\Console\Generators;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Poppy\Framework\Events\PoppyMake;
use Poppy\Framework\Exceptions\ModuleNotFoundException;
use Poppy\Framework\Poppy\Poppy;
use Symfony\Component\Console\Helper\ProgressBar;

class MakePoppyCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'poppy:make
        {slug : The slug of the module}
        {--Q|quick : Skip the make:module wizard and use default values}';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Create a new Poppy module and bootstrap it';

	/**
	 * The poppy instance.
	 * @var Poppy
	 */
	protected $poppy;

	/**
	 * The filesystem instance.
	 * @var Filesystem
	 */
	protected $files;

	/**
	 * Array to store the configuration details.
	 * @var array
	 */
	protected $container;

	/**
	 * Create a new command instance.
	 * @param Filesystem $files
	 * @param Poppy      $poppy
	 */
	public function __construct(Filesystem $files, Poppy $poppy)
	{
		parent::__construct();

		$this->files = $files;
		$this->poppy = $poppy;
	}

	/**
	 * Execute the console command.
	 * @return mixed
	 * @throws FileNotFoundException
	 */
	public function handle()
	{
		$slug = str_slug($this->argument('slug'));
		if (app('poppy')->exists($slug)) {
			$this->error('Slug `' . $slug . '` exists');
			return false;
		}

		$this->container['slug']        = str_slug($this->argument('slug'));
		$this->container['name']        = snake_case($this->container['slug']);
		$this->container['version']     = '1.0';
		$this->container['description'] = 'This is the description for the poppy ' . $this->container['name'] . ' module.';

		if ($this->option('quick')) {
			$this->container['basename']  = snake_case($this->container['slug']);
			$this->container['namespace'] = studly_case($this->container['basename']);

			return $this->generate();
		}

		$this->displayHeader('make_module_introduction');

		$this->stepOne();
	}

	/**
	 * Step 1: Configure module manifest.
	 * @return mixed
	 * @throws FileNotFoundException
	 */
	protected function stepOne()
	{
		$this->displayHeader('make_module_step_1');

		$this->container['name']        = $this->ask('Please enter the name of the module:', $this->container['name']);
		$this->container['slug']        = $this->ask('Please enter the slug for the module:', $this->container['slug']);
		$this->container['version']     = $this->ask('Please enter the module version:', $this->container['version']);
		$this->container['description'] = $this->ask('Please enter the description of the module:', $this->container['description']);
		$this->container['namespace']   = studly_case($this->container['slug']);

		$this->comment('You have provided the following manifest information:');
		$this->comment('Name:                       ' . $this->container['name']);
		$this->comment('Slug:                       ' . $this->container['slug']);
		$this->comment('Version:                    ' . $this->container['version']);
		$this->comment('Description:                ' . $this->container['description']);
		$this->comment('Namespace (auto-generated): ' . $this->container['namespace']);

		if ($this->confirm('If the provided information is correct, type "yes" to generate.')) {
			$this->comment('Thanks! That\'s all we need.');
			$this->comment('Now relax while your module is generated.');

			$this->generate();
		}
		else {
			return $this->stepOne();
		}

		return true;
	}

	/**
	 * Generate the module.
	 */
	protected function generate()
	{
		$steps = [
			'Generating module...'       => 'generateModule',
			'Optimizing module cache...' => 'optimizeModules',
		];

		$progress = new ProgressBar($this->output, count($steps));
		$progress->start();

		foreach ($steps as $message => $function) {
			$progress->setMessage($message);

			$this->$function();

			$progress->advance();
		}

		$progress->finish();

		event(new PoppyMake($this->container['slug']));

		$this->info("\nPoppy Module generated successfully.");
	}

	/**
	 * Generate defined module folders.
	 * @throws ModuleNotFoundException
	 */
	protected function generateModule()
	{
		if (!$this->files->isDirectory(poppy_path())) {
			$this->files->makeDirectory(poppy_path());
		}

		$directory = poppy_path(null, $this->container['slug']);
		$source    = __DIR__ . '/../../../resources/stubs/poppy';

		$this->files->makeDirectory($directory);

		$sourceFiles = $this->files->allFiles($source, true);

		foreach ($sourceFiles as $file) {
			$contents = $this->replacePlaceholders($file->getContents());
			$subPath  = $file->getRelativePathname();

			$filePath = $directory . '/' . $subPath;
			$dir      = dirname($filePath);

			if (!$this->files->isDirectory($dir)) {
				$this->files->makeDirectory($dir, 0755, true);
			}

			$this->files->put($filePath, $contents);
		}
	}

	/**
	 * Reset module cache of enabled and disabled modules.
	 */
	protected function optimizeModules()
	{
		return $this->callSilent('poppy:optimize');
	}

	/**
	 * Pull the given stub file contents and display them on screen.
	 * @param string $file
	 * @param string $level
	 * @return mixed
	 * @throws FileNotFoundException
	 */
	protected function displayHeader($file = '', $level = 'info')
	{
		$stub = $this->files->get(__DIR__ . '/../../../resources/stubs/console/' . $file . '.stub');

		return $this->$level($stub);
	}

	protected function replacePlaceholders($contents)
	{
		$find = [
			'DummyNamespace',
			'DummyName',
			'DummySlug',
			'DummyVersion',
			'DummyDescription',
		];

		$replace = [
			$this->container['namespace'],
			$this->container['name'],
			$this->container['slug'],
			$this->container['version'],
			$this->container['description'],
		];

		return str_replace($find, $replace, $contents);
	}
}
