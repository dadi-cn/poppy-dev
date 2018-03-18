<?php namespace Poppy\Framework\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Migrations\Migrator;
use Poppy\Framework\Classes\Traits\MigrationTrait;
use Poppy\Framework\Poppy\Poppy;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PoppyMigrateRollbackCommand extends Command
{
	use MigrationTrait, ConfirmableTrait;

	/**
	 * The console command name.
	 * @var string
	 */
	protected $name = 'poppy:migrate:rollback';

	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'Rollback the last database migrations for a specific or all modules';

	/**
	 * The migrator instance.
	 * @var \Illuminate\Database\Migrations\Migrator
	 */
	protected $migrator;

	/**
	 * @var Poppy
	 */
	protected $poppy;

	/**
	 * Create a new command instance.
	 * @param Migrator $migrator
	 * @param Poppy    $poppy
	 */
	public function __construct(Migrator $migrator, Poppy $poppy)
	{
		parent::__construct();

		$this->migrator = $migrator;
		$this->poppy    = $poppy;
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

		$this->migrator->setConnection($this->option('database'));

		$paths = $this->getMigrationPaths();
		$this->migrator->rollback(
			$paths,
			['pretend' => $this->option('pretend'), 'step' => (int) $this->option('step')]
		);

		foreach ($this->migrator->getNotes() as $note) {
			$this->output->writeln($note);
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
			['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
			['force', null, InputOption::VALUE_NONE, 'Force the operation to run while in production.'],
			['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
			['step', null, InputOption::VALUE_OPTIONAL, 'The number of migrations to be reverted.'],
		];
	}

	/**
	 * Get all of the migration paths.
	 * @return array
	 * @throws \Poppy\Framework\Exceptions\ModuleNotFoundException
	 */
	protected function getMigrationPaths()
	{
		$slug  = $this->argument('slug');
		$paths = [];

		if ($slug) {
			$paths[] = poppy_path($slug, 'database/migrations');
		}
		else {
			foreach ($this->poppy->all() as $module) {
				$paths[] = poppy_path($module['slug'], 'database/migrations');
			}
		}

		return $paths;
	}
}
