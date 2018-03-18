<?php namespace Poppy\Framework\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;
use Poppy\Framework\Poppy\Poppy;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PoppyMigrateResetCommand extends Command
{
	use ConfirmableTrait;

	/**
	 * The console command name.
	 * @var string
	 */
	protected $name = 'poppy:migrate:reset';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Rollback all database migrations for a specific or all modules';

	/**
	 * @var Poppy
	 */
	protected $poppy;

	/**
	 * @var Migrator
	 */
	protected $migrator;

	/**
	 * @var Filesystem
	 */
	protected $files;

	/**
	 * Create a new command instance.
	 * @param Poppy      $poppy
	 * @param Filesystem $files
	 * @param Migrator   $migrator
	 */
	public function __construct(Poppy $poppy, Filesystem $files, Migrator $migrator)
	{
		parent::__construct();

		$this->poppy    = $poppy;
		$this->files    = $files;
		$this->migrator = $migrator;
	}

	/**
	 * Execute the console command.
	 * @return mixed
	 * @throws \Poppy\Framework\Exceptions\ModuleNotFoundException
	 */
	public function handle()
	{
		if (!$this->confirmToProceed()) {
			return;
		}

		$this->reset();
	}

	/**
	 * Run the migration reset for the current list of slugs.
	 * Migrations should be reset in the reverse order that they were
	 * migrated up as. This ensures the database is properly reversed
	 * without conflict.
	 * @return mixed
	 * @throws \Poppy\Framework\Exceptions\ModuleNotFoundException
	 */
	protected function reset()
	{
		$this->migrator->setconnection($this->input->getOption('database'));

		$files = $this->migrator->getMigrationFiles($this->getMigrationPaths());

		$migrations = array_reverse($this->migrator->getRepository()->getRan());

		if (count($migrations) == 0) {
			$this->output->writeln('Nothing to rollback.');
		}
		else {
			$this->migrator->requireFiles($files);

			foreach ($migrations as $migration) {
				if (!array_key_exists($migration, $files)) {
					continue;
				}

				$this->runDown($files[$migration], (object) ['migration' => $migration]);
			}
		}

		foreach ($this->migrator->getNotes() as $note) {
			$this->output->writeln($note);
		}
	}

	/**
	 * Run "down" a migration instance.
	 * @param string $file
	 * @param object $migration
	 */
	protected function runDown($file, $migration)
	{
		$file     = $this->migrator->getMigrationName($file);
		$instance = $this->migrator->resolve($file);

		$instance->down();

		$this->migrator->getRepository()->delete($migration);

		$this->info('Rolledback: ' . $file);
	}

	/**
	 * Generate a list of all migration paths, given the arguments/operations supplied.
	 * @return array
	 * @throws \Poppy\Framework\Exceptions\ModuleNotFoundException
	 */
	protected function getMigrationPaths()
	{
		$migrationPaths = [];

		foreach ($this->getSlugsToReset() as $slug) {
			$migrationPaths[] = $this->getMigrationPath($slug);

			event($slug . '.poppy.reset', [$this->poppy, $this->option()]);
		}

		return $migrationPaths;
	}

	/**
	 * Using the arguments, generate a list of slugs to reset the migrations for.
	 * @return array
	 */
	protected function getSlugsToReset()
	{
		if ($this->validSlugProvided()) {
			return [$this->argument('slug')];
		}

		if ($this->option('force')) {
			return $this->poppy->all()->pluck('slug');
		}

		return $this->poppy->enabled()->pluck('slug');
	}

	/**
	 * Determine if a valid slug has been provided as an argument.
	 * We will accept a slug as long as it is not empty and is enabled (or force is passed).
	 * @return bool
	 */
	protected function validSlugProvided()
	{
		if (empty($this->argument('slug'))) {
			return false;
		}

		if ($this->poppy->isEnabled($this->argument('slug'))) {
			return true;
		}

		if ($this->option('force')) {
			return true;
		}

		return false;
	}

	/**
	 * Get the console command parameters.
	 * @param string $slug
	 * @return array
	 * @throws \Poppy\Framework\Exceptions\ModuleNotFoundException
	 */
	protected function getParameters($slug)
	{
		$params = [];

		$params['--path'] = $this->getMigrationPath($slug);

		if ($option = $this->option('database')) {
			$params['--database'] = $option;
		}

		if ($option = $this->option('pretend')) {
			$params['--pretend'] = $option;
		}

		if ($option = $this->option('seed')) {
			$params['--seed'] = $option;
		}

		return $params;
	}

	/**
	 * Get migrations path.
	 * @return string
	 * @throws \Poppy\Framework\Exceptions\ModuleNotFoundException
	 */
	protected function getMigrationPath($slug)
	{
		return poppy_path($slug, 'database/migrations');
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
			['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
			['force', null, InputOption::VALUE_NONE, 'Force the operation to run while in production.'],
			['pretend', null, InputOption::VALUE_OPTIONAL, 'Dump the SQL queries that would be run.'],
			['seed', null, InputOption::VALUE_OPTIONAL, 'Indicates if the seed task should be re-run.'],
		];
	}
}
