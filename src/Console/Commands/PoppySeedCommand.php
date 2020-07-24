<?php namespace Poppy\Framework\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Poppy\Framework\Poppy\Poppy;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Poppy Seed
 */
class PoppySeedCommand extends Command
{
	/**
	 * The console command name.
	 * @var string
	 */
	protected $name = 'poppy:seed';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Seed the database with records for a specific or all modules';

	/**
	 * @var Poppy
	 */
	protected $poppy;

	/**
	 * Create a new command instance.
	 * @param Poppy $poppy
	 */
	public function __construct(Poppy $poppy)
	{
		parent::__construct();

		$this->poppy = $poppy;
	}

	/**
	 * Execute the console command.
	 * @return mixed
	 */
	public function handle()
	{
		$slug = $this->argument('slug');

		if (isset($slug)) {
			if (!$this->poppy->exists($slug)) {
				return $this->error('Module does not exist.');
			}

			if ($this->poppy->isEnabled($slug)) {
				$this->seed($slug);
			}
			elseif ($this->option('force')) {
				$this->seed($slug);
			}

			return;
		}

		if ($this->option('force')) {
			$modules = $this->poppy->all();
		}
		else {
			$modules = $this->poppy->enabled();
		}

		foreach ($modules as $module) {
			$this->seed($module['slug']);
		}
	}

	/**
	 * Seed the specific module.
	 * @param string $slug slug
	 */
	protected function seed($slug)
	{
		$module        = $this->poppy->where('slug', $slug);
		$params        = [];
		$namespacePath = $this->poppy->getNamespace();

		$rootSeeder = ucfirst(Str::camel($module['basename'])) . 'DatabaseSeeder';
		$fullPath   = ucfirst(Str::camel($namespacePath)) . '\\' . ucfirst(Str::camel($module['basename'])) . '\Database\Seeds\\' . $rootSeeder;

		if (class_exists($fullPath)) {
			if ($this->option('class')) {
				$params['--class'] = $this->option('class');
			}
			else {
				$params['--class'] = $fullPath;
			}

			if ($option = $this->option('database')) {
				$params['--database'] = $option;
			}

			if ($option = $this->option('force')) {
				$params['--force'] = $option;
			}

			$this->call('db:seed', $params);

			event($slug . '.module.seeded', [$module, $this->option()]);
		}
	}

	/**
	 * Get the console command arguments.
	 * @return array
	 */
	protected function getArguments()
	{
		return [['slug', InputArgument::OPTIONAL, 'Module slug.']];
	}

	/**
	 * Get the console command options.
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['class', null, InputOption::VALUE_OPTIONAL, 'The class name of the module\'s root seeder.'],
			['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to seed.'],
			['force', null, InputOption::VALUE_NONE, 'Force the operation to run while in production.'],
		];
	}
}
